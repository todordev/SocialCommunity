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
 * This class contains methods that are used for managing location.
 *
 * @package      SocialCommunity
 * @subpackage   Locations
 */
class SocialCommunityLocation
{
    protected $id;
    protected $name;
    protected $latitude;
    protected $longitude;
    protected $country_code;
    protected $state_code;
    protected $timezone;
    protected $published;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object.
     *
     * <code>
     * $locationId = 1;
     * $location   = new SocialCommunityLocation(JFactory::getDbo());
     * $location->load($locationId);
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Create an object or return existing one.
     *
     * <code>
     * $locationId = 1;
     *
     * $location   = SocialCommunityLocation::getInstance(JFactory::getDbo(), $locationId);
     * </code>
     *
     * @param JDatabaseDriver $db
     * @param int             $id
     *
     * @return null|SocialCommunityLocation
     */
    public static function getInstance(JDatabaseDriver $db, $id)
    {
        if (!isset(self::$instances[$id])) {
            $item = new SocialCommunityLocation($db);
            $item->load($id);

            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Set database object.
     *
     * <code>
     * $location   = new SocialCommunityLocation();
     * $location->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Load location data from database.
     *
     * <code>
     * $locationId = 1;
     *
     * $location   = new SocialCommunityLocation();
     * $location->setDb(JFactory::getDbo());
     * $location->load($locationId);
     * </code>
     *
     * @param int $id
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.name, a.latitude, a.longitude, a.country_code, a.state_code, a.timezone, a.published")
            ->from($this->db->quoteName("#__itpsc_locations", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data about location to object parameters.
     *
     * <code>
     * $data = array(
     *  "name"  => "London",
     *  "country_code" => "GB"
     * );
     *
     * $location   = new SocialCommunityLocation();
     * $location->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     *
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return location ID.
     *
     * <code>
     * $locationId  = 1;
     *
     * $location    = new SocialCommunityLocation(JFactory::getDbo());
     * $location->load($typeId);
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
        return $this->id;
    }

    /**
     * Return a country code.
     *
     * <code>
     * $locationId  = 1;
     *
     * $location    = new SocialCommunityLocation(JFactory::getDbo());
     * $location->load($typeId);
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
     * $locationId  = 1;
     *
     * $location    = new SocialCommunityLocation(JFactory::getDbo());
     * $location->load($typeId);
     *
     * $name = $location->getName(SocialCommunityConstants::INCLUDE_COUNTRY_CODE);
     * </code>
     *
     * @param bool $includeCountryCode A flag that indicate to be included country code to the name.
     *
     * @return string
     */
    public function getName($includeCountryCode = false)
    {
        if ($includeCountryCode and !empty($this->country_code)) {
            return $this->name .", ". $this->country_code;
        }

        return $this->name;
    }
}
