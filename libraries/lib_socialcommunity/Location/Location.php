<?php
/**
 * @package      SocialCommunity
 * @subpackage   Locations
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Socialcommunity\Location;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing location.
 *
 * @package      SocialCommunity
 * @subpackage   Locations
 */
class Location extends Prism\Database\TableImmutable
{
    protected $id;
    protected $name;
    protected $latitude;
    protected $longitude;
    protected $country_code;
    protected $state_code;
    protected $timezone;
    protected $published;

    protected static $instances = array();

    /**
     * Create an object or return existing one.
     *
     * <code>
     * $locationId = 1;
     *
     * $location   = Socialcommunity\Location\Location::getInstance(\JFactory::getDbo(), $locationId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param array            $keys
     *
     * @return null|Location
     */
    public static function getInstance(\JDatabaseDriver $db, $keys)
    {
        $id = (!array_key_exists('id', $keys)) ? 0 : (int)$keys['id'];

        if (!array_key_exists($id, self::$instances) and $id > 0) {
            $item = new Location($db);
            $item->load($keys);

            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Load location data from database.
     *
     * <code>
     * $keys = array(
     *    "id" => 1
     * );
     *
     * $location   = new Socialcommunity\Location\Location(\JFactory::getDbo());
     * $location->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.name, a.latitude, a.longitude, a.country_code, a.state_code, a.timezone, a.published')
            ->from($this->db->quoteName('#__itpsc_locations', 'a'));

        // Filter by keys.
        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) . ' = ' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }
    
    /**
     * Return location ID.
     *
     * <code>
     * $keys = array(
     *    "id" => 1
     * );
     *
     * $location    = new Socialcommunity\Location\Location(\JFactory::getDbo());
     * $location->load($keys);
     *
     * if (!$location->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return a country code.
     *
     * <code>
     * $keys = array(
     *    "id" => 1
     * );
     *
     * $location    = new Socialcommunity\Location\Location(\JFactory::getDbo());
     * $location->load($keys);
     *
     * $code = $location->getCountryCode();
     * </code>
     *
     * @return int
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Return the name of the location.
     *
     * <code>
     * $keys = array(
     *    "id" => 1
     * );
     *
     * $location    = new Socialcommunity\Location\Location(\JFactory::getDbo());
     * $location->load($keys);
     *
     * $name = $location->getName(SocialcommunityConstants::INCLUDE_COUNTRY_CODE);
     * </code>
     *
     * @param bool $includeCountryCode A flag that indicate to be included country code to the name.
     *
     * @return string
     */
    public function getName($includeCountryCode = false)
    {
        if ($includeCountryCode and ($this->country_code !== null and $this->country_code !== '')) {
            return $this->name .', '. $this->country_code;
        }

        return $this->name;
    }
}
