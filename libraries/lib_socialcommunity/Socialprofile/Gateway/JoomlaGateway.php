<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Validator\Date as DateValidator;
use Prism\Database\Joomla\FetchCollectionMethod;
use Socialcommunity\Socialprofile\Socialprofile;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements SocialprofileGateway
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
        $defaultFields  = ['a.id', 'a.service', 'a.service_user_id', 'a.link', 'a.image_square', 'a.access_token', 'a.expires_at', 'a.secret_key', 'a.user_id'];
        $fields         = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__itpsc_socialprofiles', 'a'));

        return $query;
    }

    public function insertObject(Socialprofile $object)
    {
        $link        = $object->getLink() ? $this->db->quote((string)$object->getLink()) : 'NULL';
        $imageSquare = $object->getImageSquare() ? $this->db->quote((string)$object->getImageSquare()) : 'NULL';
        $secretKey   = $object->getSecretKey() ? $this->db->quote((string)$object->getSecretKey()) : 'NULL';
        $accessToken = $object->getAccessToken() ? $this->db->quote($object->getAccessToken()->getValue()) : 'NULL';

        $expiresAt = '0000-00-00 00:00:00';
        if ($object->getAccessToken() !== null && $object->getAccessToken()->getExpiresAt() !== null) {
            $expiresAt_ = $object->getAccessToken()->getExpiresAt()->format('Y-m-d H:i:s');

            $date = new DateValidator($expiresAt_);
            if ($date->isValid()) {
                $expiresAt_ = new \JDate($expiresAt_);
                $expiresAt = $expiresAt_->toSql();
            }
        }

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_socialprofiles'))
            ->set($this->db->quoteName('link') . '=' . $link)
            ->set($this->db->quoteName('image_square') .'='. $imageSquare)
            ->set($this->db->quoteName('service_user_id') . '=' . $this->db->quote($object->getServiceUserId()))
            ->set($this->db->quoteName('service') . '=' . $this->db->quote($object->getService()))
            ->set($this->db->quoteName('access_token') . '=' . $accessToken)
            ->set($this->db->quoteName('expires_at') . '=' . $this->db->quote($expiresAt))
            ->set($this->db->quoteName('secret_key') . '=' . $secretKey)
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($object->getUserId()));

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Socialprofile $object)
    {
        $link        = $object->getLink() ? $this->db->quote((string)$object->getLink()) : 'NULL';
        $imageSquare = $object->getImageSquare() ? $this->db->quote((string)$object->getImageSquare()) : 'NULL';
        $secretKey   = $object->getSecretKey() ? $this->db->quote((string)$object->getSecretKey()) : 'NULL';
        $accessToken = $object->getAccessToken() ? $this->db->quote($object->getAccessToken()->getValue()) : 'NULL';

        $expiresAt = '0000-00-00 00:00:00';
        if ($object->getAccessToken() !== null && $object->getAccessToken()->getExpiresAt() !== null) {
            $expiresAt_ = $object->getAccessToken()->getExpiresAt()->format('Y-m-d H:i:s');

            $date = new DateValidator($expiresAt_);
            if ($date->isValid()) {
                $expiresAt_ = new \JDate($expiresAt_);
                $expiresAt = $expiresAt_->toSql();
            }
        }

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_socialprofiles'))
            ->set($this->db->quoteName('link') . '=' . $link)
            ->set($this->db->quoteName('image_square') .'='. $imageSquare)
            ->set($this->db->quoteName('service_user_id') . '=' . $this->db->quote($object->getServiceUserId()))
            ->set($this->db->quoteName('service') . '=' . $this->db->quote($object->getService()))
            ->set($this->db->quoteName('access_token') . '=' . $accessToken)
            ->set($this->db->quoteName('expires_at') . '=' . $this->db->quote($expiresAt))
            ->set($this->db->quoteName('secret_key') . '=' . $secretKey)
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId());

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
        if ($conditions->getSpecificCondition('user_ids')) {
            $condition = $conditions->getSpecificCondition('user_ids');

            if (is_array($condition->getValue())) {
                $usersIds = ArrayHelper::toInteger($condition->getValue());
                $usersIds = array_filter(array_unique($usersIds));

                if (count($usersIds) > 0) {
                    $query->where($this->db->quoteName('a.user_id') . ' IN (' . implode(',', $usersIds) . ')');
                }
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
