<?php
/**
 * @package      SocialCommunity
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class SocialCommunityActivity {

    /**
     * Activity ID.
     * @var integer
     */
    public $id;
    
    public $content;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Driver of the database
     * @var JDatabaseMySQLi
     */
    protected $db;
    
	public function __construct(JDatabase $db) {
        $this->db = $db;
    }
    
    /**
     * Load activity record.
     * 
     * @param integer $id
     */
    public function load($id) {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.content, a.image, a.url, a.created, a.user_id")
            ->from($this->db->quoteName("#__itpsc_activities", "a"))
            ->where("a.id = ". (int)$id);
            
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) {
            $this->bind($result);
        }
        
    }
    
    public function bind($data) {
        
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
        
    }
    
    protected function updateObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->update("#__itpsc_activities")
            ->set($this->db->quoteName("content") ."=". $this->db->quote($this->content) )
            ->set($this->db->quoteName("image")   ."=". $this->db->quote($this->image) )
            ->set($this->db->quoteName("url")     ."=". $this->db->quote($this->url) )
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
            ->insert("#__itpsc_activities")
            ->set($this->db->quoteName("content") ."=". $this->db->quote($this->content) )
            ->set($this->db->quoteName("created") ."=". $this->db->quote($this->created) )
            ->set($this->db->quoteName("user_id") ."=". (int)$this->user_id);
            
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("image")   ."=". $this->db->quote($this->image) );
        }
        
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("url")     ."=". $this->db->quote($this->url) );
        }
        
        $this->db->setQuery($query);
        $this->db->execute();
        
        return $this->db->insertid();
        
    }
    
    public function store() {
        
        if(!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
}

