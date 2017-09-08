<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Request\Request;
use Socialcommunity\Profile\Profile;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements ProfileGateway
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
        $query = $this->db->getQuery(true);

        $defaultFields  = [
            'a.id', 'a.name', 'a.alias', 'a.image', 'a.image_icon', 'a.image_square',
            'a.birthday', 'a.gender', 'a.location_id', 'a.country_code', 'a.website',
            'a.user_id', 'a.user_id', 'a.active'
        ];

        $aliasFields = [
            'slug' => $query->concatenate(array('a.user_id', 'a.alias'), ':') . ' AS slug'
        ];

        $fields = $this->prepareFields($request, $defaultFields, $aliasFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query
            ->select($fields)
            ->from($this->db->quoteName('#__itpsc_profiles', 'a'));

        return $query;
    }

    public function insertObject(Profile $object)
    {
        $bio    = !$object->getBio() ? 'NULL' : $this->db->quote($object->getBio());
        $date   = new \JDate($object->getBirthday());

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($object->getName()))
            ->set($this->db->quoteName('alias') . '=' . $this->db->quote($object->getAlias()))
            ->set($this->db->quoteName('bio') . '=' . $bio)
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($object->getImage()))
            ->set($this->db->quoteName('image_icon') . '=' . $this->db->quote($object->getImageIcon()))
            ->set($this->db->quoteName('image_square') . '=' . $this->db->quote($object->getImageSquare()))
            ->set($this->db->quoteName('image_small') . '=' . $this->db->quote($object->getImageSmall()))
            ->set($this->db->quoteName('birthday') . '=' . $this->db->quote($date->toSql()))
            ->set($this->db->quoteName('gender') . '=' . $this->db->quote($object->getGender()))
            ->set($this->db->quoteName('website') . '=' . $this->db->quote($object->getWebsite()))
            ->set($this->db->quoteName('active') . '=' . (int)$object->getStatus())
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->set($this->db->quoteName('location_id') . '=' . (int)$object->getLocationId())
            ->set($this->db->quoteName('country_code') . '=' . $this->db->quote($object->getCountryCode()));

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Profile $object)
    {
        $bio   = (!$object->getBio()) ? 'NULL' : $this->db->quote($object->getBio());
        $date  = new \JDate($object->getBirthday());

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($object->getName()))
            ->set($this->db->quoteName('alias') . '=' . $this->db->quote($object->getAlias()))
            ->set($this->db->quoteName('bio') . '=' . $bio)
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($object->getImage()))
            ->set($this->db->quoteName('image_icon') . '=' . $this->db->quote($object->getImageIcon()))
            ->set($this->db->quoteName('image_square') . '=' . $this->db->quote($object->getImageSquare()))
            ->set($this->db->quoteName('image_small') . '=' . $this->db->quote($object->getImageSmall()))
            ->set($this->db->quoteName('birthday') . '=' . $this->db->quote($date->toSql()))
            ->set($this->db->quoteName('gender') . '=' . $this->db->quote($object->getGender()))
            ->set($this->db->quoteName('website') . '=' . $this->db->quote($object->getWebsite()))
            ->set($this->db->quoteName('active') . '=' . (int)$object->getStatus())
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->set($this->db->quoteName('location_id') . '=' . (int)$object->getLocationId())
            ->set($this->db->quoteName('country_code') . '=' . $this->db->quote($object->getCountryCode()))
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

        // Filter by country IDs
        if ($conditions->getSpecificCondition('country_codes')) {
            $condition = $conditions->getSpecificCondition('country_codes');
            $codes     = $condition->getValue();

            if (is_array($codes)) {
                $escapedCodes = array_map(function ($value) {
                    return $this->db->quote($value);
                }, $codes);

                if (count($escapedCodes) > 0) {
                    $query->where($this->db->quoteName('a.country_codes') .' IN ('. implode(',', $escapedCodes) .')');
                }
            }

            unset($codes);
        }

        // Filter by location IDs
        if ($conditions->getSpecificCondition('location_ids')) {
            $condition = $conditions->getSpecificCondition('location_ids');
            if (is_array($condition->getValue())) {
                $locationIds = ArrayHelper::toInteger($condition->getValue());
                $locationIds = array_filter(array_unique($locationIds));

                if (count($locationIds) > 0) {
                    $query->where($this->db->quoteName('a.location_id') . ' IN (' . implode(',', $locationIds) . ')');
                }
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
