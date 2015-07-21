<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Install script file of the component
 */
class pkg_socialCommunityInstallerScript
{
    protected $imagesFolder;
    protected $imagesPath;

    /**
     * Method to install the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {

        if (!defined("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR")) {
            define("SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_socialcommunity");
        }

        // Register Install Helper
        JLoader::register("SocialCommunityInstallHelper", SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "install.php");

        jimport('Prism.init');
        jimport('Crowdfunding.init');

        $params             = JComponentHelper::getParams("com_socialcommunity");
        /** @var  $params Joomla\Registry\Registry */

        $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/profiles"));
        $this->imagesPath   = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $this->imagesFolder);

        // Create images folder
        if (!is_dir($this->imagesPath)) {
            SocialCommunityInstallHelper::createFolder($this->imagesPath);
        }

        // Start table with the information
        SocialCommunityInstallHelper::startTable();

        // Requirements
        SocialCommunityInstallHelper::addRowHeading(JText::_("COM_SOCIALCOMMUNITY_MINIMUM_REQUIREMENTS"));

        // Display result about verification for existing folder
        $title = JText::_("COM_SOCIALCOMMUNITY_IMAGE_FOLDER");
        $info  = $this->imagesFolder;
        if (!is_dir($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writeable folder
        $title = JText::_("COM_SOCIALCOMMUNITY_WRITABLE_FOLDER");
        $info  = $this->imagesFolder;
        if (!is_writable($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for GD library
        $title = JText::_("COM_SOCIALCOMMUNITY_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_SOCIALCOMMUNITY_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_SOCIALCOMMUNITY_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_SOCIALCOMMUNITY_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_SOCIALCOMMUNITY_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_("COM_SOCIALCOMMUNITY_FILEINFO");
        $info  = "";
        if (!function_exists('finfo_open')) {
            $info   = JText::_("COM_SOCIALCOMMUNITY_FILEINFO_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP Version.
        $title = JText::_("COM_SOCIALCOMMUNITY_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_SOCIALCOMMUNITY_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed Prism Library
        $title = JText::_("COM_SOCIALCOMMUNITY_PRISM_LIBRARY");
        $info  = "";
        if (!class_exists("Prism\\Version")) {
            $info   = JText::_("COM_SOCIALCOMMUNITY_PRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        SocialCommunityInstallHelper::addRowHeading(JText::_("COM_SOCIALCOMMUNITY_INSTALLED_EXTENSIONS"));

        // SocialCommunity Library
        $result = array("type" => "success", "text" => JText::_("COM_SOCIALCOMMUNITY_INSTALLED"));
        SocialCommunityInstallHelper::addRow(JText::_("COM_SOCIALCOMMUNITY_SOCIALCOMMUNITY_LIBRARY"), $result, JText::_("COM_SOCIALCOMMUNITY_LIBRARY"));

        // User - Social Community New User
        $result = array("type" => "success", "text" => JText::_("COM_SOCIALCOMMUNITY_INSTALLED"));
        SocialCommunityInstallHelper::addRow(JText::_("COM_SOCIALCOMMUNITY_USER_SOCIALCOMMUNITY_USER"), $result, JText::_("COM_SOCIALCOMMUNITY_PLUGIN"));

        // End table with the information
        SocialCommunityInstallHelper::endTable();

        echo JText::sprintf("COM_SOCIALCOMMUNITY_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_socialcommunity"));

        if (!class_exists("Prism\\Version")) {
            echo JText::_("COM_SOCIALCOMMUNITY_MESSAGE_INSTALL_PRISM_LIBRARY");
        } else {

            if (class_exists("SocialCommunity\\Version")) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new SocialCommunity\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion)) {
                    echo JText::_("COM_SOCIALCOMMUNITY_MESSAGE_INSTALL_PRISM_LIBRARY");
                }
            }
        }
    }
}
