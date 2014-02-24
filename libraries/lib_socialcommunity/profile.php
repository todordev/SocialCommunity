<?php
/**
 * @package      SocialCommunity
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class SocialCommunityProfile {

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
    
    protected $db;
    
    protected static $instances = array();
    
    public function __construct(JDatabase $db){
        $this->db = $db;
    }
    
    public static function getInstance($db, $id = 0)  {
    
        if (empty(self::$instances[$id])){
            $item = new SocialCommunityProfile($db);
            $item->load($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
    }
    
    public function load($id) {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select(
                "a.id, a.name, a.alias, a.image, a.image_icon, a.image_square, a.image_small, a.bio, a.phone, " .
                "a.address, a.birthday, a.gender, a.location_id, a.country_id, a.website, " .
                $query->concatenate(array("a.id", "a.alias"), "-") . ' AS slug')
            ->from($this->db->quoteName("#__itpsc_profiles", "a"))
            ->where("a.id = ". (int)$id);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) {
            $this->bind($result);
        }
        
    }
    
    public function bind($data, $exclude = array()) {
        
        foreach($data as $key => $value) {
            
            if(!in_array($key, $exclude)) {
                $this->$key = $value;
            }
            
        }
        
    }
    
    /**
     * @return the $slug
     */
    public function getSlug() {
        return $this->slug;
    }
    
	/**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

	/**
     * @return the $name
     */
    public function getName() {
        return $this->name;
    }

	/**
     * @return the $alias
     */
    public function getAlias() {
        return $this->alias;
    }

	/**
     * @return the $image
     */
    public function getImage() {
        return $this->image;
    }

	/**
     * @return the $image_icon
     */
    public function getImage_icon() {
        return $this->image_icon;
    }

	/**
     * @return the $image_square
     */
    public function getImage_square() {
        return $this->image_square;
    }

	/**
     * @return the $image_small
     */
    public function getImage_small() {
        return $this->image_small;
    }

	/**
     * @return the $bio
     */
    public function getBio() {
        return $this->bio;
    }

	/**
     * @return the $phone
     */
    public function getPhone() {
        return $this->phone;
    }

	/**
     * @return the $address
     */
    public function getAddress() {
        return $this->address;
    }

	/**
     * @return the $birthday
     */
    public function getBirthday() {
        return $this->birthday;
    }

	/**
     * @return the $gender
     */
    public function getGender() {
        return $this->gender;
    }

	/**
     * @return the $location_id
     */
    public function getLocation_id() {
        return $this->location_id;
    }

	/**
     * @return the $country_id
     */
    public function getCountry_id() {
        return $this->country_id;
    }

	/**
     * @return the $website
     */
    public function getWebsite() {
        return $this->website;
    }

}
