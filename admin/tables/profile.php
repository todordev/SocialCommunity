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
    
        $db     = $this->getDbo();
        // Create a new query object.
        $query  = $db->getQuery(true);
        $query
            ->insert($db->quoteName("#__itpsc_profiles"))
            ->set($db->quoteName("id")   ." = " . (int)$this->id)
            ->set($db->quoteName("name") ." = " . $db->quote($this->name))
            ->set($db->quoteName("alias")." = " . $db->quote($this->alias));
    
        $db->setQuery($query);
        $db->query();
    
    }
    
    /**
     * @return the $slug
     */
    public function getSlug() {
        return $this->slug;
    }
    
    /**
     * @param string $slug
     */
    public function setSlug($slug) {
        $this->slug = $slug;
    }
    
}