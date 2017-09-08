<?php
/**
 * @package      Socialcommunity\Post
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post\Gateway;

use Socialcommunity\Post\Post;
use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Post
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements PostGateway
{
    use FetchMethods, FetchCollectionMethod;

    /**
     * Prepare the query by query builder.
     *
     * @param Request $request
     *
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery(Request $request = null)
    {
        $defaultFields  = ['a.id', 'a.content', 'a.media', 'a.url', 'a.created_at', 'a.user_id'];
        $fields         = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__itpsc_posts', 'a'));

        return $query;
    }

    public function insertObject(Post $object)
    {
        $content   = $object->getContent() ? $this->db->quote($object->getContent()) : 'NULL';
        $url       = $object->getUrl() ? $this->db->quote($object->getUrl()) : 'NULL';
        $media     = $object->getMedia() ? $this->db->quote($object->getMedia()) : 'NULL';
        $createdAt = $object->createdAt() ? $this->db->quote($object->createdAt()) : 'NULL';

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_posts'))
            ->set($this->db->quoteName('content') . '=' . $content)
            ->set($this->db->quoteName('url') . '=' . $url)
            ->set($this->db->quoteName('media') . '=' . $media)
            ->set($this->db->quoteName('created_at') . '=' . $createdAt)
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Post $object)
    {
        $content   = $object->getContent() ? $this->db->quote($object->getContent()) : 'NULL';
        $url       = $object->getUrl() ? $this->db->quote($object->getUrl()) : 'NULL';
        $media     = $object->getMedia() ? $this->db->quote($object->getMedia()) : 'NULL';
        $createdAt = $object->createdAt() ? $this->db->quote($object->createdAt()) : 'NULL';

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_posts'))
            ->set($this->db->quoteName('content') . '=' . $content)
            ->set($this->db->quoteName('url') . '=' . $url)
            ->set($this->db->quoteName('media') . '=' . $media)
            ->set($this->db->quoteName('created_at') . '=' . $createdAt)
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }

    public function deleteObject(Post $object)
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_posts'))
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId())
            ->where($this->db->quoteName('user_id') . '=' . (int)$object->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Prepare some query filters.
     *
     * @param \JDatabaseQuery $query
     * @param Request         $request
     *
     * @throws \InvalidArgumentException
     */
    protected function filter(\JDatabaseQuery $query, Request $request)
    {
        $conditions = $request->getConditions();

        // Filter by IDs
        if ($conditions->getSpecificCondition('ids')) {
            $condition = $conditions->getSpecificCondition('ids');
            if (is_array($condition->getValue())) {
                $ids = ArrayHelper::toInteger($condition->getValue());
                $ids = array_filter(array_unique($ids));

                if (count($ids) > 0) {
                    $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
                }
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
