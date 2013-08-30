<?php
/**
 * @package      SocialCommunity
 * @subpackage   Plugin
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

/**
 * This plugin creating a Social Community profile.
 *
 * @package		SocialCommunity
 * @subpackage	Plugin
 */
class plgUserSocialCommunityNewUser extends JPlugin {
	
	/**
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg) {
	    
		if ($isnew) {
		    
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
	       ->from($db->quoteName("#__users") . " AS a")
	       ->leftJoin($db->quoteName("#__itpsc_profiles") ." AS b ON a.id = b.id")
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
	    
	    jimport('socialcommunity.profile');
	    	
	    $data = array(
            'id'       => (int)$userId,
            'name'	   => $name,
            'alias'	   => JApplication::stringURLSafe($name)
	    );
	    
	    $profile = new SocialCommunityProfile();
	    $profile->bind($data);
	    	
	    $profile->create();
	    
	}
}
