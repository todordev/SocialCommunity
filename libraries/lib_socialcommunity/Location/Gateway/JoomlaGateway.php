<?php
/**
 * @package      Socialcommunity\Location
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Location\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Location
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements LocationGateway
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
        $defaultFields  = ['a.id', 'a.name', 'a.latitude', 'a.longitude', 'a.country_code', 'a.timezone'];
        $fields         = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__itpsc_locations', 'a'));

        return $query;
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

        // Filter by country code.
        if ($conditions->getSpecificCondition('country_code')) {
            $condition = $conditions->getSpecificCondition('country_code');

            $query->where($this->db->quoteName('a.country_code') .'='. $this->db->quote($condition->getValue()));
        }

        // Filter by search phrase (search by name).
        if ($conditions->getSpecificCondition('search')) {
            $condition = $conditions->getSpecificCondition('search');

            $escaped = $this->db->escape($condition->getValue(), true);
            $quoted  = $this->db->quote('%' . $escaped . '%', false);
            $query->where('a.name LIKE ' . $quoted);
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
