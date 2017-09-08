<?php
/**
 * @package      Socialcommunity\Country\Data\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Country\Data\Gateway\Joomla;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Request\Request;
use Socialcommunity\Country\Data\Gateway\CountriesGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Country\Data\Gateway
 * @subpackage   Joomla
 */
class Countries extends JoomlaDatabaseGateway implements CountriesGateway
{
    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * // Prepare conditions.
     * $fieldCode = new \Prism\Database\Request\Field(['column' => 'code']);
     * $fieldName = new \Prism\Database\Request\Field(['column' => 'name']);
     *
     * $fields    = new \Prism\Database\Request\Fields;
     * $fields
     * ->addField($fieldCode)
     * ->addField($fieldName);
     *
     * $databaseRequest = new \Prism\Database\Request\Request;
     * $databaseRequest
     *    ->setFields($fields)
     *    ->setConditions($fields);
     *
     * $gateway = new Socialcommunity\Country\Data\Gateway\Joomla\Countries(\JFactory::getDbo());
     * $items   = $gateway->fetch($databaseRequest);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetch(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no fields that the system should use to fetch data.');
        }

        $query = $this->getQuery($request);
        $this->filter($query, $request);

        $this->db->setQuery($query);

        return (array)$this->db->loadAssocList();
    }

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
        $defaultFields  = ['a.id', 'a.name', 'a.code'];
        $fields         = $this->prepareFields($request, $defaultFields);
        
        $query = $this->db->getQuery(true);
        $query
            ->select($this->db->quoteName($fields))
            ->from($this->db->quoteName('#__itpsc_countries', 'a'));

        return $query;
    }

    /**
     * Prepare some query filters.
     *
     * @param \JDatabaseQuery $query
     * @param Request         $request
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
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

        // Filter by search phrase (column Name).
        if ($conditions->getSpecificCondition('search')) {
            $condition = $conditions->getSpecificCondition('code');

            $escaped = $this->db->escape($condition->getValue(), true);
            $quoted  = $this->db->quote('%' . $escaped . '%', false);
            $query->where('a.name LIKE ' . $quoted);
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
