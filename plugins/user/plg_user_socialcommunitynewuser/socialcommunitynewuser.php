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
		    
			jimport('socialcommunity.profile');
			
			$name = JArrayHelper::getValue($user, 'name');
			
			$data = array(
			    'id'       => JArrayHelper::getValue($user, 'id'),
			    'name'	   => $name,
			    'alias'	   => JApplication::stringURLSafe($name)
			);
    		
			$profile = new SocialCommunityProfile();
			$profile->bind($data);
			
			$profile->create();
			
		}
		
	}

}
