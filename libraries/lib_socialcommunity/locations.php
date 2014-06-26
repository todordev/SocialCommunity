<?php
/**
 * @package      SocialCommunity
 * @subpackage   Locations
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage locations.
 *
 * @package      SocialCommunity
 * @subpackage   Locations
 */
class SocialCommunityLocations implements Iterator, Countable, ArrayAccess
{
    protected $items = array();

    protected $position = 0;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $locations   = new SocialCommunityLocations(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Load currencies data by ID from database.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     *
     * $locations   = new SocialCommunityLocations(JFactory::getDbo());
     * $locations->load($ids);
     *
     * foreach($locations as $location) {
     *   echo $location["name"];
     *   echo $location["country_code"];
     * }
     * </code>
     *
     * @param array $ids
     */
    public function load($ids = array())
    {
        JArrayHelper::toInteger($ids);

        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.latitude, a.longitude, a.country_code, a.state_code, a.timezone, a.published")
            ->from($this->db->quoteName("#__itpsc_locations", "a"));

        if (!empty($ids)) {
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function count()
    {
        return (int)count($this->items);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Create a location object and return it.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     * $locations   = new SocialCommunityLocations(JFactory::getDbo());
     * $locations->load($ids);
     *
     * $location = $locations->getLocation(1);
     * </code>
     *
     * @param string $id
     *
     * @return null|SocialCommunityLocation
     */
    public function getLocation($id)
    {
        if (!$id) {
            return null;
        }

        $location = null;

        foreach ($this->items as $item) {

            if ($id == $item["id"]) {

                $location = new SocialCommunityLocation();
                $location->bind($item);

                break;
            }

        }

        return $location;
    }

    /**
     * Return locations as option values.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     * 
     * $locations   = new SocialCommunityLocations(JFactory::getDbo());
     * $locations->load($ids);
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
