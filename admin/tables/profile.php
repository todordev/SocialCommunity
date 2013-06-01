<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die;

class SocialCommunityTableProfile extends JTable {
    
    protected $slug;
    
	public function __construct($db) {
        parent::__construct('#__itpsc_profiles', 'id', $db);
    }
    
    public function load($keys = null, $reset = true) {
        
        parent::load($keys, $reset);
        
        if(!empty($this->id)) {
            $this->slug = $this->id."-".$this->alias;
        }
        
    }
    
    /**
     * Crete a new user record in the database. 
     */
    public function create() {
    
        // Create a new query object.
        $query  = $this->_db->getQuery(true);
        $query
            ->insert($this->_db->quoteName("#__itpsc_profiles"))
            ->set($this->_db->quoteName("id")   ." = " . (int)$this->id)
            ->set($this->_db->quoteName("name") ." = " . $this->_db->quote($this->name))
            ->set($this->_db->quoteName("alias")." = " . $this->_db->quote($this->alias));
    
        $this->_db->setQuery($query);
        $this->_db->query();
    
    }
    
    /**
     * @return the $slug
     */
    public function getSlug() {
        return $this->slug;
    }
    
    /**
     * @param field_type $slug
     */
    public function setSlug($slug) {
        $this->slug = $slug;
    }
    
}