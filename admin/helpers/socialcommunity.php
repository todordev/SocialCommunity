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

// no direct access
defined('_JEXEC') or die;

class SocialCommunityHelper {
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_SOCIALCOMMUNITY_DASHBOARD'),
			'index.php?option=com_socialcommunity&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_SOCIALCOMMUNITY_PROFILES'),
			'index.php?option=com_socialcommunity&view=profiles',
			$vName == 'profiles'
		);
		
		JSubMenuHelper::addEntry(
    		JText::_('COM_SOCIALCOMMUNITY_PLUGINS'),
    		'index.php?option=com_plugins&view=plugins&filter_search='.rawurlencode("social community"),
    		$vName == 'plugins'
        );
	}
	
}