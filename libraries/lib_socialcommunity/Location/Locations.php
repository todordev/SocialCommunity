<?php
/**
 * @package      SocialCommunity
 * @subpackage   Locations
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Location;

use Prism;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage locations.
 *
 * @package      SocialCommunity
 * @subpackage   Locations
 */
class Locations extends Prism\Database\Collection
{
    /**
     * Load locations data by ID from database.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5), // Load locations by IDs.
     *     "search" => "London" // It is a phrase for searching.
     * );
     *
     * $locations   = new Socialcommunity\Locations(JFactory::getDbo());
     * $locations->load($options);
     *
     * foreach($locations as $location) {
     *   echo $location["name"];
     *   echo $location["country_code"];
     * }
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $ids    = ArrayHelper::getValue($options, 'ids', array(), 'array');
        $search = ArrayHelper::getValue($options, 'search', '', 'string');
        $countryId = ArrayHelper::getValue($options, 'country_id', 0, 'int');

        ArrayHelper::toInteger($ids);

        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.name, a.latitude, a.longitude, a.country_code, a.state_code, a.timezone, a.published')
            ->from($this->db->quoteName('#__itpsc_locations', 'a'));

        if (count($ids) > 0) {
            $query->where('a.id IN ( ' . implode(',', $ids) . ' )');
        }

        // Filter by country ID ( use subquery to get country code ).
        if ($countryId > 0) {
            $subQuery = $this->db->getQuery(true);

            $subQuery
                ->select('sqc.code')
                ->from($this->db->quoteName('#__itpsc_countries', 'sqc'))
                ->where('sqc.id = ' .(int)$countryId);
            
            $query->where('a.country_code = ( ' . $subQuery . ' )');
        }

        if ($query !== null and $query !== '') {
            $escaped = $this->db->escape($search, true);
            $quoted  = $this->db->quote('%' . $escaped . '%', false);
            $query->where('a.name LIKE ' . $quoted);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList('id');
    }

    /**
     * Create a location object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     * 
     * $locations   = new Socialcommunity\Locations(JFactory::getDbo());
     * $locations->load($options);
     *
     * $location = $locations->getLocation(1);
     * </code>
     *
     * @param string $id
     *
     * @return null|Location
     */
    public function getLocation($id)
    {
        $location = null;
        
        if (array_key_exists($id, $this->items)) {
            $location = new Location($this->db);
            $location->bind($this->items[$id]);
        }

        return $location;
    }
}
