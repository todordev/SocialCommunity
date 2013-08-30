<?php
/**
 * @package      Social Community
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Social Community Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR")) {
    define("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_socialcommunity");
}

if(!defined("SOCIALCOMMUNITY_PATH_COMPONENT_SITE")) {
    define("SOCIALCOMMUNITY_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_socialcommunity");
}

if(!defined("SOCIALCOMMUNITY_PATH_LIBRARY")) {
    define("SOCIALCOMMUNITY_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "socialcommunity");
}

if(!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Register Component libraries
JLoader::register("SocialCommunityVersion", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");

// Register helpers
JLoader::register("SocialCommunityHelper", SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "socialcommunity.php");
JLoader::register("SocialCommunityHelperRoute", SOCIALCOMMUNITY_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");