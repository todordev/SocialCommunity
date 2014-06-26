<?php
/**
 * @package      SocialCommunity
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if (!defined("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR")) {
    define("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_socialcommunity");
}

if (!defined("SOCIALCOMMUNITY_PATH_COMPONENT_SITE")) {
    define("SOCIALCOMMUNITY_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_socialcommunity");
}

if (!defined("SOCIALCOMMUNITY_PATH_LIBRARY")) {
    define("SOCIALCOMMUNITY_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "socialcommunity");
}

jimport('joomla.utilities.arrayhelper');

// Register Component libraries
JLoader::register("SocialCommunityConstants", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "constants.php");
JLoader::register("SocialCommunityVersion", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");
JLoader::register("SocialCommunityLocation", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "location.php");
JLoader::register("SocialCommunityLocations", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "locations.php");
JLoader::register("SocialCommunityProfile", SOCIALCOMMUNITY_PATH_LIBRARY . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "profile.php");

// Register helpers
JLoader::register("SocialCommunityHelper", SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "socialcommunity.php");
JLoader::register("SocialCommunityHelperRoute", SOCIALCOMMUNITY_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

// Register Observers
JLoader::register(
    "SocialCommunityObserverProfile",
    SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "tables" .DIRECTORY_SEPARATOR. "observers" .DIRECTORY_SEPARATOR. "profile.php"
);
JObserverMapper::addObserverClassToClass('SocialCommunityObserverProfile', 'SocialCommunityTableProfile', array('typeAlias' => 'com_socialcommunity.profile'));

// Include HTML helpers path
JHtml::addIncludePath(SOCIALCOMMUNITY_PATH_COMPONENT_SITE . '/helpers/html');