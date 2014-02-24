<?php
/**
 * @package      SocialCommunity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * This plugin creating a Social Community profile.
 *
 * @package		SocialCommunity
 * @subpackage	Plugins
 */
class plgUserSocialCommunityNewUser extends JPlugin {
	
	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function onUserAfterSave($user, $isNew, $success, $msg) {
	    
		if ($isNew) {
		    
		    if(!JComponentHelper::isEnabled("com_socialcommunity")) {
		        return;
		    }
		    
		    $userId = JArrayHelper::getValue($user, "id");
		    $name   = JArrayHelper::getValue($user, "name");
		    $this->createProfile($userId, $name);
			
		}
		
	}
	
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user        Holds the user data
	 * @param   array  $options     Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 * @since   1.5
	 */
	public function onUserLogin($user, $options) {
	    
	    if(!JComponentHelper::isEnabled("com_socialcommunity")) {
	        return true;
	    }
	    
	    $db    = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    
	    $query
	       ->select("a.id, b.id AS profile_id")
	       ->from($db->quoteName("#__users", "a"))
	       ->leftJoin($db->quoteName("#__itpsc_profiles", "b") ." ON a.id = b.id")
	       ->where("a.username = " .$db->quote($user["username"]));
	    
	    $db->setQuery($query, 0, 1);
	    $result = $db->loadAssoc();
	    
	    // Create profile
	    if(empty($result["profile_id"])) {
	        $userId = JArrayHelper::getValue($result, "id");
	        $name   = JArrayHelper::getValue($user, "fullname");
	        $this->createProfile($userId, $name);
	    }
	    	    
	    return true;
	    
	}

	private function createProfile($userId, $name) {
	    
	    $db     = JFactory::getDbo();
	    $query  = $db->getQuery(true);
	    
	    $query
    	    ->insert($db->quoteName("#__itpsc_profiles"))
    	    ->set($db->quoteName("id")    ."=". (int)$userId)
    	    ->set($db->quoteName("name")  ."=". $db->quote($name))
    	    ->set($db->quoteName("alias") ."=". $db->quote(JApplication::stringURLSafe($name)));
	    
	    $db->setQuery($query);
	    $db->execute();
	    
	}
}
