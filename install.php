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

/**
 * Install script file of the component
 */
class pkg_socialCommunityInstallerScript {
    
        /**
         * method to install the component
         *
         * @return void
         */
        public function install($parent) {
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        public function uninstall($parent) {
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        public function update($parent) {
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        public function preflight($type, $parent) {
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        public function postflight($type, $parent) {

            if(!defined("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR")) {
                define("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR."com_socialcommunity");
            }
            
            // Register Install Helper
            JLoader::register("SocialCommunityInstallHelper", SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "helpers" .DIRECTORY_SEPARATOR. "install.php");
            
            jimport('joomla.filesystem.path');
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            
            $params             = JComponentHelper::getParams("com_socialcommunity");
            $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/profiles"));
            $this->imagesPath   = JPath::clean(JPATH_SITE .DIRECTORY_SEPARATOR. $this->imagesFolder);
            
            // Create images folder
            if(!is_dir($this->imagesPath)){
                SocialCommunityInstallHelper::createFolder($this->imagesPath);
            }
            
            // Start table with the information
            SocialCommunityInstallHelper::startTable();
            
            // Requirements
            SocialCommunityInstallHelper::addRowHeading(JText::_("COM_SOCIALCOMMUNITY_MINIMUM_REQUIREMENTS"));
            
            // Display result about verification for existing folder
            $title  = JText::_("COM_SOCIALCOMMUNITY_IMAGE_FOLDER");
            $info   = $this->imagesFolder;
            if(!is_dir($this->imagesPath)) {
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JYES"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for writeable folder
            $title  = JText::_("COM_SOCIALCOMMUNITY_WRITABLE_FOLDER");
            $info   = $this->imagesFolder;
            if(!is_writable($this->imagesPath)) {
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JYES"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for GD library
            $title  = JText::_("COM_SOCIALCOMMUNITY_GD_LIBRARY");
            $info   = "";
            if(!extension_loaded('gd') AND function_exists('gd_info')) {
                $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for cURL library
            $title  = JText::_("COM_SOCIALCOMMUNITY_CURL_LIBRARY");
            $info   = "";
            if( !extension_loaded('curl') ) {
                $info   = JText::_("COM_SOCIALCOMMUNITY_CURL_INFO");
                $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification Magic Quotes
            $title  = JText::_("COM_SOCIALCOMMUNITY_MAGIC_QUOTES");
            $info   = "";
            if( get_magic_quotes_gpc() ) {
                $info   = JText::_("COM_SOCIALCOMMUNITY_MAGIC_QUOTES_INFO");
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JOFF"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification FileInfo
            $title  = JText::_("COM_SOCIALCOMMUNITY_FILEINFO");
            $info   = "";
            if( !function_exists('finfo_open') ) {
                $info   = JText::_("COM_SOCIALCOMMUNITY_FILEINFO_INFO");
                $result = array("type" => "important", "text" => JText::_("JOFF"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JON"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification FileInfo
            $title  = JText::_("COM_SOCIALCOMMUNITY_PHP_VERSION");
            $info   = "";
            if (version_compare(PHP_VERSION, '5.3.0') < 0) {
                $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JYES"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification of installed ITPrism Library
            jimport("itprism.version");
            $title  = JText::_("COM_SOCIALCOMMUNITY_ITPRISM_LIBRARY");
            $info   = "";
            if( !class_exists("ITPrismVersion") ) {
                $info   = JText::_("COM_SOCIALCOMMUNITY_ITPRISM_LIBRARY_DOWNLOAD");
                $result = array("type" => "important", "text" => JText::_("JNO"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JYES"));
            }
            SocialCommunityInstallHelper::addRow($title, $result, $info);
            
            // Installed extensions
            
            SocialCommunityInstallHelper::addRowHeading(JText::_("COM_SOCIALCOMMUNITY_INSTALLED_EXTENSIONS"));
            
            // SocialCommunity Library
            $result = array("type" => "success"  , "text" => JText::_("COM_SOCIALCOMMUNITY_INSTALLED"));
            SocialCommunityInstallHelper::addRow(JText::_("COM_SOCIALCOMMUNITY_SOCIALCOMMUNITY_LIBRARY"), $result, JText::_("COM_SOCIALCOMMUNITY_LIBRARY"));
            
            // User - Social Community New User
            $result = array("type" => "success"  , "text" => JText::_("COM_SOCIALCOMMUNITY_INSTALLED"));
            SocialCommunityInstallHelper::addRow(JText::_("COM_SOCIALCOMMUNITY_USER_SOCIALCOMMUNITY_NEW_USER"), $result, JText::_("COM_SOCIALCOMMUNITY_PLUGIN"));
            
            // End table with the information
            SocialCommunityInstallHelper::endTable();
                
            echo JText::sprintf("COM_SOCIALCOMMUNITY_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_socialcommunity"));
            
        }
}
