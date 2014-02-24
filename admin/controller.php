<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Default Controller
 *
 * @package		 SocialCommunity
 * @subpackage   Components
  */
class SocialCommunityController extends JControllerLegacy {
    
	public function display( ) {

		$app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $option   = $app->input->getCmd("option");
        
        $document = JFactory::getDocument();
		/** @var $document JDocumentHtml **/
        
        // Add component style
        $document->addStyleSheet('../media/'.$option.'/css/admin/style.css');
        
        $viewName      = $app->input->getCmd('view', 'dashboard');
        $app->input->set("view", $viewName);

        parent::display();
        return $this;
	}

}