<?php
/**
 * @package      SocialCommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace SocialCommunity;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing profile.
 *
 * @package      SocialCommunity
 * @subpackage   Profiles
 */
class Profile extends Prism\Database\TableImmutable
{
    protected $id;
    protected $name;
    protected $alias;
    protected $image;
    protected $image_icon;
    protected $image_square;
    protected $image_small;
    protected $bio;
    protected $phone;
    protected $address;
    protected $birthday;
    protected $gender;
    protected $location_id;
    protected $country_id;
    protected $website;
    protected $slug;
    
    static protected $instances = array();

    /**
     * Create and initialize an object.
     *
     * <code>
     * $userId = 1;
     *
     * $profile   = SocialCommunity\Profile::getInstance(\JFactory::getDbo(), $userId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param integer $id
     * 
     * @return Profile
     */
    public static function getInstance(\JDatabaseDriver $db, $id)
    {
        if (is_null(self::$instances[$id])) {
            
            $item = new Profile($db);
            $item->load($id);
            
            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Load notification record from database.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.name, a.alias, a.image, a.image_icon, a.image_square, a.image_small, a.bio, a.phone, " .
                "a.address, a.birthday, a.gender, a.location_id, a.country_id, a.website, " .
                $query->concatenate(array("a.id", "a.alias"), "-") . ' AS slug'
            )
            ->from($this->db->quoteName("#__itpsc_profiles", "a"));

        // Filter by keys.
        if (!is_array($keys)) {
            $query->where("a.id = " . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName($key) . " = " . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }

    }

    /**
     * Get an unique profile alias used in profile URI.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $slug = $profile->getSlug();
     * </code>
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get profile ID.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * if (!$profile->getID()) {
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
     * Get the name of the user.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $name = $profile->getName();
     * </code>
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get unique alias of the user.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $alias = $profile->getAlias();
     * </code>
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Get a user picture.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $image = $profile->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return profile icon.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $image = $profile->getImageIcon();
     * </code>
     *
     * @return string
     */
    public function getImageIcon()
    {
        return $this->image_icon;
    }

    /**
     * Return profile square image.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $image = $profile->getImageSquare();
     * </code>
     *
     * @return string
     */
    public function getImageSquare()
    {
        return $this->image_square;
    }

    /**
     * Return profile small image.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $image = $profile->getImageSmall();
     * </code>
     *
     * @return string
     */
    public function getImageSmall()
    {
        return $this->image_small;
    }

    /**
     * Return information about user ( biography )
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $bio = $profile->getBio();
     * </code>
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Return phone number of an user.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $phone = $profile->getPhone();
     * </code>
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Return information about user address.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $address = $profile->getAddress();
     * </code>
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Return the date of user birthday.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $birthday = $profile->getBirthday();
     * </code>
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Return user gender - male or female.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $gender = $profile->getGender();
     * </code>
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Return ID of user location.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $locationId = $profile->getLocationId();
     * </code>
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Return ID of user country.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $countryId = $profile->getCountryId();
     * </code>
     *
     * @return int
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Return user website.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $website = $profile->getWebsite();
     * </code>
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Remove user picture from database and filesystem.
     *
     * <code>
     * $profileId = 1;
     * $imagesFolder = "/root/home/user/john/www/images/profile/";
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeImages($imagesFolder);
     * </code>
     * 
     * @param string $imagesFolder
     * 
     */
    public function removeImages($imagesFolder)
    {
        // Delete old image if I upload a new one
        if (!empty($this->image)) {

            jimport('joomla.filesystem.file');

            // Remove an image from the filesystem
            $fileImage  = $imagesFolder . DIRECTORY_SEPARATOR . $this->image;
            $fileSmall  = $imagesFolder . DIRECTORY_SEPARATOR . $this->image_small;
            $fileIcon   = $imagesFolder . DIRECTORY_SEPARATOR . $this->image_icon;
            $fileSquare = $imagesFolder . DIRECTORY_SEPARATOR . $this->image_square;

            if (\JFile::exists($fileImage)) {
                \JFile::delete($fileImage);
            }

            if (\JFile::exists($fileSmall)) {
                \JFile::delete($fileSmall);
            }

            if (\JFile::exists($fileIcon)) {
                \JFile::delete($fileIcon);
            }

            if (\JFile::exists($fileSquare)) {
                \JFile::delete($fileSquare);
            }

            $this->image = null;
            $this->image_small = null;
            $this->image_icon = null;
            $this->image_square = null;
        }
    }

    /**
     * Remove user activities.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeActivities();
     * </code>
     */
    public function removeActivities()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__itpsc_activities"))
            ->where($this->db->quoteName("user_id") ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove user notifications.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeNotifications();
     * </code>
     */
    public function removeNotifications()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__itpsc_notifications"))
            ->where($this->db->quoteName("user_id") ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove the records of user social profiles.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeSocialProfiles();
     * </code>
     */
    public function removeSocialProfiles()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__itpsc_socialprofiles"))
            ->where($this->db->quoteName("user_id") ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * This method creates a profile.
     *
     * <code>
     * $data = array(
     *     "id" => 123,
     *     "name" => "John Dow",
     *     "alias" => "john-dow"
     * );
     *
     * $profile = new SocialCommunity\Profile(\JFactory::getDbo());
     * $profile->bind($data);
     *
     * $profile->create();
     * </code>
     */
    public function create()
    {
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__itpsc_profiles"))
            ->set($this->db->quoteName("id") . "=" . (int)$this->id)
            ->set($this->db->quoteName("name") . "=" . $this->db->quote($this->name))
            ->set($this->db->quoteName("alias") . "=" . $this->db->quote($this->alias));

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
