<?php
/**
 * @package      Socialcommunity
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (!defined('SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR')) {
    define('SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_socialcommunity');
}

if (!defined('SOCIALCOMMUNITY_PATH_COMPONENT_SITE')) {
    define('SOCIALCOMMUNITY_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_socialcommunity');
}

if (!defined('SOCIALCOMMUNITY_PATH_LIBRARY')) {
    define('SOCIALCOMMUNITY_PATH_LIBRARY', JPATH_LIBRARIES . '/socialcommunity');
}

JLoader::registerNamespace('Socialcommunity', JPATH_LIBRARIES);

// Register helpers
JLoader::register('SocialcommunityHelper', SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . '/helpers/socialcommunity.php');
JLoader::register('SocialcommunityHelperRoute', SOCIALCOMMUNITY_PATH_COMPONENT_SITE . '/helpers/route.php');

// Register Observers
JLoader::register('SocialcommunityObserverProfile', SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . '/tables/observers/profile.php');
JObserverMapper::addObserverClassToClass('SocialcommunityObserverProfile', 'SocialcommunityTableProfile', array('typeAlias' => 'com_socialcommunity.profile'));

// Include HTML helpers path
JHtml::addIncludePath(SOCIALCOMMUNITY_PATH_COMPONENT_SITE . '/helpers/html');

// Load library language
$lang = JFactory::getLanguage();
$lang->load('lib_socialcommunity', SOCIALCOMMUNITY_PATH_LIBRARY);
