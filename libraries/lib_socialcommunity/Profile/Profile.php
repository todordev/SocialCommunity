<?php
/**
 * @package      SocialCommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

use Coinbase\Wallet\Exception\NotFoundException;
use League\Flysystem\FileNotFoundException;
use Prism;
use Socialcommunity\Validator;
use League\Flysystem\Filesystem;
use Defuse\Crypto\Crypto;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing profile.
 *
 * @package      SocialCommunity
 * @subpackage   Profiles
 */
class Profile extends Prism\Database\Table
{
    protected $id = 0;
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
    protected $user_id = 0;
    protected $location_id = 0;
    protected $country_id = 0;
    protected $website;
    protected $slug;
    protected $active = 0;

    protected $secretKey;

    static protected $instances = array();

    /**
     * Create and initialize an object.
     *
     * <code>
     * $userId = 1;
     *
     * $profile   = Socialcommunity\Profile\Profile::getInstance(\JFactory::getDbo(), $userId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param integer $id
     * 
     * @return Profile
     */
    public static function getInstance(\JDatabaseDriver $db, $id)
    {
        if (!array_key_exists($id, self::$instances)) {
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.name, a.alias, a.image, a.image_icon, a.image_square, a.image_small, a.bio, a.phone, ' .
                'a.address, a.birthday, a.gender, a.location_id, a.country_id, a.website, a.user_id, a.active, ' .
                $query->concatenate(array('a.user_id', 'a.alias'), '-') . ' AS slug'
            )
            ->from($this->db->quoteName('#__itpsc_profiles', 'a'));

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

        // Decrypt phone and address.
        if ($this->secretKey) {
            $this->phone   = (!$this->phone) ? null : $this->db->quote(Crypto::decrypt($this->phone, $this->secretKey));
            $this->address = (!$this->address) ? null : $this->db->quote(Crypto::decrypt($this->address, $this->secretKey));
        }
    }

    /**
     * Store data to database.
     *
     * <code>
     * $data = array(
     *    "user_id" => 1,
     *    "name" => "John Dow"
     * );
     *
     * $profile    = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->bind($data);
     * $profile->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $image          = (!$this->image) ? 'NULL' : $this->db->quote($this->image);
        $imageIcon      = (!$this->image_icon) ? 'NULL' : $this->db->quote($this->image_icon);
        $imageSquare    = (!$this->image_square) ? 'NULL' : $this->db->quote($this->image_square);
        $imageSmall     = (!$this->image_small) ? 'NULL' : $this->db->quote($this->image_small);
        $bio            = (!$this->bio) ? 'NULL' : $this->db->quote($this->bio);
        $gender         = (!in_array($this->gender, array('male', 'female'), true)) ? $this->db->quote('male') : $this->db->quote($this->gender);
        $active         = ((int)$this->active === 0) ? 0 : 1;

        // Prepare valid alias.
        $this->alias = Helper::safeAlias($this->alias);

        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($this->name))
            ->set($this->db->quoteName('alias') . '=' . $this->db->quote($this->alias))
            ->set($this->db->quoteName('image') . '=' . $image)
            ->set($this->db->quoteName('image_icon') . '=' . $imageIcon)
            ->set($this->db->quoteName('image_square') . '=' . $imageSquare)
            ->set($this->db->quoteName('image_small') . '=' . $imageSmall)
            ->set($this->db->quoteName('bio') . '=' . $bio)
            ->set($this->db->quoteName('birthday') . '=' . $this->db->quote($this->birthday))
            ->set($this->db->quoteName('gender') . '=' . $gender)
            ->set($this->db->quoteName('website') . '=' . $this->db->quote($this->website))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('active') . '=' . (int)$active)
            ->set($this->db->quoteName('location_id') . '=' . (int)$this->location_id)
            ->set($this->db->quoteName('country_id') . '=' . (int)$this->country_id);

        // Encrypt phone and address.
        if ($this->secretKey) {
            $phone   = (!$this->phone)   ? null : $this->db->quote(Crypto::encrypt($this->phone, $this->secretKey));
            $address = (!$this->address) ? null : $this->db->quote(Crypto::encrypt($this->address, $this->secretKey));

            $query
                ->set($this->db->quoteName('phone') . '=' . $phone)
                ->set($this->db->quoteName('address') . '=' . $address);
        }

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $image = (!$this->image) ? 'NULL' : $this->db->quote($this->image);
        $imageIcon   = (!$this->image_icon) ? 'NULL' : $this->db->quote($this->image_icon);
        $imageSquare   = (!$this->image_square) ? 'NULL' : $this->db->quote($this->image_square);
        $imageSmall   = (!$this->image_small) ? 'NULL' : $this->db->quote($this->image_small);
        $bio   = (!$this->bio) ? 'NULL' : $this->db->quote($this->bio);
        $gender   = (!in_array($this->gender, array('male', 'female'), true)) ? $this->db->quote('male') : $this->db->quote($this->gender);
        $active   = ((int)$this->active === 0) ? 0 : 1;

        // Prepare valid alias.
        $this->alias = Helper::safeAlias($this->alias, $this->user_id);

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($this->name))
            ->set($this->db->quoteName('alias') . '=' . $this->db->quote($this->alias))
            ->set($this->db->quoteName('image') . '=' . $image)
            ->set($this->db->quoteName('image_icon') . '=' . $imageIcon)
            ->set($this->db->quoteName('image_square') . '=' . $imageSquare)
            ->set($this->db->quoteName('image_small') . '=' . $imageSmall)
            ->set($this->db->quoteName('bio') . '=' . $bio)
            ->set($this->db->quoteName('birthday') . '=' . $this->db->quote($this->birthday))
            ->set($this->db->quoteName('gender') . '=' . $gender)
            ->set($this->db->quoteName('website') . '=' . $this->db->quote($this->website))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('active') . '=' . (int)$active)
            ->set($this->db->quoteName('location_id') . '=' . (int)$this->location_id)
            ->set($this->db->quoteName('country_id') . '=' . (int)$this->country_id)
            ->where($this->db->quoteName('id') .'='. (int)$this->id);

        // Encrypt phone and address.
        if ($this->secretKey) {
            $phone   = (!$this->phone)   ? null : $this->db->quote(Crypto::encrypt($this->phone, $this->secretKey));
            $address = (!$this->address) ? null : $this->db->quote(Crypto::encrypt($this->address, $this->secretKey));

            $query
                ->set($this->db->quoteName('phone') . '=' . $phone)
                ->set($this->db->quoteName('address') . '=' . $address);
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Get an unique profile alias used in profile URI.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * Get record ID.
     *
     * <code>
     * $userId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load(array('user_id' => $userId));
     *
     * if (!$profile->getId()) {
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
     * Get user ID.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * echo $profile->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set the user ID of the user.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->setUserId(1);
     * </code>
     *
     * @param int $id
     *
     * @return self
     */
    public function setUserId($id)
    {
        $this->user_id = (int)$id;

        return $this;
    }

    /**
     * Get the name of the user.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * Set the name of the user.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->setName('John Dow');
     * </code>
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get unique alias of the user.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * Set the unique alias of the user.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->setAlias('john-dow');
     * </code>
     *
     * @param string $alias
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Return secret key used for encrypting or decrypting sensitive data.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * echo $profile->setSecretKey();
     * </code>
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Set a secret key that will be used to encrypt or decrypt sensitive data.
     *
     * <code>
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->setSecretKey('secret_key');
     * </code>
     *
     * @param string $key
     *
     * @return self
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;

        return $this;
    }

    /**
     * Get a user picture.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $locationId = $profile->getLocationId();
     * </code>
     *
     * @return int
     */
    public function getLocationId()
    {
        return (int)$this->location_id;
    }

    /**
     * Return ID of user country.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $countryId = $profile->getCountryId();
     * </code>
     *
     * @return int
     */
    public function getCountryId()
    {
        return (int)$this->country_id;
    }

    /**
     * Return user website.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
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
     * Check for active user profile.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * if (!$profile->isActive()) {
     * ....
     * }
     * </code>
     *
     * @return string
     */
    public function isActive()
    {
        return (bool)$this->active;
    }

    /**
     * Remove user picture from database and filesystem.
     *
     * <code>
     * $profileId   = 1;
     * $mediaFolder = "images/profile/user300";
     * $filesystem  = SocialCommunityHelper::getStorageFilesystem();
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeImages($filesystem, $mediaFolder);
     * </code>
     *
     * @throws FileNotFoundException
     *
     * @param Filesystem $filesystem
     * @param string $mediaFolder
     */
    public function removeImages($filesystem, $mediaFolder)
    {
        // Delete profile images.
        if ((string)$this->image !== '') {
            // Remove an image from the filesystem
            $files = array(
                $mediaFolder .'/'. $this->image,
                $mediaFolder .'/'. $this->image_small,
                $mediaFolder .'/'. $this->image_icon,
                $mediaFolder .'/'. $this->image_square,
            );

            foreach ($files as $file) {
                if ($filesystem->has($file)) {
                    $filesystem->delete($file);
                }
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
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeActivities();
     * </code>
     *
     * @throws \RuntimeException
     */
    public function removeActivities()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_activities'))
            ->where($this->db->quoteName('user_id') .'='. (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove user notifications.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeNotifications();
     * </code>
     *
     * @throws \RuntimeException
     */
    public function removeNotifications()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_notifications'))
            ->where($this->db->quoteName('user_id') .'='. (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove the records of user social profiles.
     *
     * <code>
     * $profileId = 1;
     *
     * $profile   = new Socialcommunity\Profile\Profile(\JFactory::getDbo());
     * $profile->load($profileId);
     *
     * $profile->removeSocialProfiles();
     * </code>
     *
     * @throws \RuntimeException
     */
    public function removeSocialProfiles()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_socialprofiles'))
            ->where($this->db->quoteName('user_id') .'='. (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
