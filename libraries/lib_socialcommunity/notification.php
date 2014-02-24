<?php
/**
 * @package      SocialCommunity
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class SocialCommunityNotification {

    const ARCHIVED      = 1;
    const NOT_ARCHIVED  = 0;
    
    /**
     * Notification ID
     * 
     * @var integer
     */
    public $id;
    
    public $content;
    public $status   = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;
    
	public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Load notification record from database.
     * 
     * @param integer $id
     */
    public function load($id) {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("a.*")
            ->from($this->db->quoteName("#__itpsc_notifications", "a"))
            ->where("a.id = ". (int)$id);
            
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) { 
            $this->bind($result);
        }
        
    }
    
    public function reset() {
        
        $parameters = get_object_vars($this);
        
        foreach($parameters as $key) {
            
            if(strcmp("db", $key)) {
                continue;
            }
            
            $this->$key = null;
        }
        
        $this->status  = 0;
        
    }
    
    public function bind($data, $exclude = array()) {
        
        foreach($data as $key => $value) {
            if(in_array($key, $exclude)) {
                continue;
            }
            
            $this->$key = $value;
        }
        
    }
    
    protected function updateObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->update($this->db->quoteName("#__itpsc_notifications"))
            ->set($this->db->quoteName("content") ."=". $this->db->quote($this->content) )
            ->set($this->db->quoteName("image")   ."=". $this->db->quote($this->image) )
            ->set($this->db->quoteName("url")     ."=". $this->db->quote($this->url) )
            ->set($this->db->quoteName("status")  ."=". (int)$this->status)
            ->set($this->db->quoteName("user_id") ."=". (int)$this->user_id)
            ->where($this->db->quoteName("id")    ."=". (int)$this->id);
            
        $this->db->setQuery($query);
        $this->execute();
    }
    
    protected function insertObject() {
        
        if(!$this->user_id) {
            throw new RuntimeException(JText::_("LIB_SOCIALCOMMUNITY_INVALID_USER_ID"));
        }
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        if(!$this->created) {
            $date = new JDate();
            $this->created = $date->toSql();
        }
        
        $query
            ->insert($this->db->quoteName("#__itpsc_notifications"))
            ->set($this->db->quoteName("note")    ." = " . $this->db->quote($this->note) )
            ->set($this->db->quoteName("created") ." = " . $this->db->quote($this->created) )
            ->set($this->db->quoteName("status")  ." = " . (int)$this->status)
            ->set($this->db->quoteName("user_id") ." = " . (int)$this->user_id);
            
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("image")   ." = " . $this->db->quote($this->image) );
        }
        
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("url")     ." = " . $this->db->quote($this->url) );
        }
        
        $this->db->setQuery($query);
        $this->db->execute();
        
        return $this->db->insertid();
        
    }
    
    protected function store() {
        
        if(!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
    
    public function remove() {
        
        if(!$this->id) {
            throw new RuntimeException(JText::_("LIB_SOCIALCOMMUNITY_INVALID_NOTIFICATION_ID"));
        }
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__itpsc_notifications"))
            ->where($this->db->quoteName("id") ."=". (int)$this->id);
        
        $this->db->setQuery($query);
        $this->db->execute();
        
        $this->reset();
        
    }
    
    /**
     * Initialize main variables, create a new notification 
     * and send it to user.
     * 
     * @param string  $note
     * @param integer $userId    This is the receiver of the message.
     */
    public function send() {
        $this->store();
    }
    
    public function setUserId($userId) {
        $this->user_id = $userId;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function isArchived() {
        return (!$this->status) ? false : true;
    }
    
	/**
     * @param field_type $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

	/**
     * @param field_type $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

	/**
     * @param Ambigous <string, mixed> $created
     */
    public function setCreated($created) {
        $this->created = $created;
    }

    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
}