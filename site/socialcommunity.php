<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('SocialCommunity.init');

$controller = JControllerLegacy::getInstance('SocialCommunity');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
