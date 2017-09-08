<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Socialcommunity.init');

$controller = JControllerLegacy::getInstance('Socialcommunity');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
