<?php
/**
* @package      SocialCommunity
* @subpackage   Libraries
* @author       Todor Iliev
* @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provieds functionality that manage social profiles.
 */
class SocialCommunitySocialProfiles implements Iterator, Countable, ArrayAccess {
    
    protected $items  = array();
    
    /**
     * Database driver.
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected $position = 0;
    
    /**
     * Initialize the object.
     * 
     * @param JDatabase Database object.
     */
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }

    /**
     * Load data from database.
     * 
     * @param integer User ID.
     */
    public function load($id) {
        
        // Load project data
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.alias, a.type")
            ->from($this->db->quoteName("#__itpsc_socialprofiles", "a"))
            ->where("a.user_id = " .(int)$id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        if(!$results) {
            $results = array();
        }
        
        $this->items = $results;
    }
    
    public function rewind() {
        $this->position = 0;
    }
    
    public function current() {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        ++$this->position;
    }
    
    public function valid() {
        return isset($this->items[$this->position]);
    }
    
    public function count() {
        return (int)count($this->items);
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
    
    /**
     * Return a social profile alias by type.
     *
     * @return NULL|string
     */
    public function getAlias($type) {
    
        foreach($this->items as $item) {
            if(strcmp($type, $item->alias)) {
                return $item->alias;
            }
        }
        
        return null;
        
    }
    
}
