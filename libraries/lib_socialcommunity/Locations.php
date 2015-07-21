<?php
/**
 * @package      SocialCommunity
 * @subpackage   Locations
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace SocialCommunity;

use Prism;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage locations.
 *
 * @package      SocialCommunity
 * @subpackage   Locations
 */
class Locations extends Prism\Database\ArrayObject
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
     * $locations   = new SocialCommunity\Locations(JFactory::getDbo());
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
    public function load($options = array())
    {
        $ids    = ArrayHelper::getValue($options, "ids", array(), "array");
        $search = ArrayHelper::getValue($options, "search", "", "string");
        
        ArrayHelper::toInteger($ids);

        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.latitude, a.longitude, a.country_code, a.state_code, a.timezone, a.published")
            ->from($this->db->quoteName("#__itpsc_locations", "a"));

        if (!empty($ids)) {
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        } elseif (!empty($query)) {
            $escaped = $this->db->escape($search, true);
            $quoted  = $this->db->quote("%" . $escaped . "%", false);

            $query->where('a.name LIKE ' . $quoted);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList("id");

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Create a location object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     * 
     * $locations   = new SocialCommunity\Locations(JFactory::getDbo());
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
        
        if (isset($this->items[$id])) {
            $location = new Location($this->db);
            $location->bind($this->items[$id]);
        }

        return $location;
    }

    /**
     * Return locations as option values.
     *
     * <code>
     * $options = array(
     *     "search" => "London"
     * );
     * 
     * $locations   = new SocialCommunity\Locations(JFactory::getDbo());
     * $locations->load($options);
     *
     * $locations = $locations->toOptions();
     * </code>
     *
     * @return array
     */
    public function toOptions()
    {
        $result = array();

        foreach ($this->items as $item) {

            if (!$item["state_code"]) {
                $result[] = array(
                    "id" => $item["id"],
                    "name" => $item["name"] .", ". $item["country_code"]
                );
            } else {
                $result[] = array(
                    "id" => $item["id"],
                    "name" => $item["name"] .", ". $item["state_code"] .", ". $item["country_code"]
                );
            }

        }

        return $result;
    }
}
