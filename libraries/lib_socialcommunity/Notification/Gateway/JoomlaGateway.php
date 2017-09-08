<?php
/**
 * @package      Socialcommunity\Notification
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\JoomlaDatabaseGateway;
use Socialcommunity\Notification\Notification;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Notification
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements NotificationGateway
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
        $defaultFields  = ['a.id', 'a.content', 'a.image', 'a.url', 'a.created', 'a.status', 'a.user_id'];
        $fields         = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__itpsc_notifications', 'a'));

        return $query;
    }

    public function insertObject(Notification $object)
    {
        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_notifications'))
            ->set($this->db->quoteName('content') .'='. $this->db->quote($object->getContent()))
            ->set($this->db->quoteName('status') .'='. (int)$object->getStatus())
            ->set($this->db->quoteName('user_id') .'='. (int)$object->getUserId());

        if (!empty($object->getImage())) {
            $query->set($this->db->quoteName('image') .'='. $this->db->quote($object->getImage()));
        }

        if (!empty($object->getUrl())) {
            $query->set($this->db->quoteName('url') .'='. $this->db->quote($object->getUrl()));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Notification $object)
    {
        $image = (!$object->getImage()) ? 'NULL' : $this->db->quote($object->getImage());
        $url   = (!$object->getUrl()) ? 'NULL' : $this->db->quote($object->getUrl());

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_notifications'))
            ->set($this->db->quoteName('content') .'='. $this->db->quote($object->getContent()))
            ->set($this->db->quoteName('created') .'='. $this->db->quote($object->getCreated()))
            ->set($this->db->quoteName('status') .'='. (int)$object->getStatus())
            ->set($this->db->quoteName('user_id') .'='. (int)$object->getUserId())
            ->set($this->db->quoteName('image') .'='. $image)
            ->set($this->db->quoteName('url') .'='. $url)
            ->where($this->db->quoteName('id') .'='. (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }

    public function deleteObject(Notification $object)
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_notifications'))
            ->where($this->db->quoteName('id') .'='. (int)$object->getId());

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
