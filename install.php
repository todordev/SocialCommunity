<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Install script file of the component
 */
class pkg_socialCommunityInstallerScript
{
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
        if (!defined('SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR')) {
            define('SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_socialcommunity');
        }

        // Register Install Helper
        JLoader::register('SocialCommunityInstallHelper', SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'install.php');

        jimport('Prism.init');
        jimport('Socialcommunity.init');

        $params       = JComponentHelper::getParams('com_socialcommunity');
        /** @var  $params Joomla\Registry\Registry */

        $mediaFolder              = JFolder::makeSafe($params->get('local_media_folder', 'media/socialcommunity'));
        $mediaFolderPath          = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $mediaFolder);

        $temporaryMediaFolder     = $mediaFolder . '/temporary';
        $temporaryMediaFolderPath = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $temporaryMediaFolder);

        // Create images folder
        if (!JFolder::exists($mediaFolderPath)) {
            SocialCommunityInstallHelper::createFolder($mediaFolderPath);
        }

        // Create temporary images folder
        if (!JFolder::exists($temporaryMediaFolderPath)) {
            SocialCommunityInstallHelper::createFolder($temporaryMediaFolderPath);
        }
        
        // Start table with the information
        SocialCommunityInstallHelper::startTable();

        // Requirements
        SocialCommunityInstallHelper::addRowHeading(JText::_('COM_SOCIALCOMMUNITY_MINIMUM_REQUIREMENTS'));

        // Display result about verification for existing folder
        $title = JText::_('COM_SOCIALCOMMUNITY_IMAGE_FOLDER');
        $info  = $mediaFolder;
        if (!JFolder::exists($mediaFolderPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writable folder
        $title = JText::_('COM_SOCIALCOMMUNITY_WRITABLE_FOLDER');
        $info  = $mediaFolder;
        if (!is_writable($mediaFolderPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for existing folder
        $title = JText::_('COM_SOCIALCOMMUNITY_TEMPORARY_IMAGE_FOLDER');
        $info  = $temporaryMediaFolder;
        if (!JFolder::exists($temporaryMediaFolderPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writable folder
        $title = JText::_('COM_SOCIALCOMMUNITY_TEMPORARY_WRITABLE_FOLDER');
        $info  = $temporaryMediaFolder;
        if (!is_writable($temporaryMediaFolderPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for GD library
        $title = JText::_('COM_SOCIALCOMMUNITY_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and !function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_SOCIALCOMMUNITY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_SOCIALCOMMUNITY_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_SOCIALCOMMUNITY_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('COM_SOCIALCOMMUNITY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_SOCIALCOMMUNITY_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_SOCIALCOMMUNITY_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_('COM_SOCIALCOMMUNITY_FILEINFO');
        $info  = '';
        if (!function_exists('finfo_open')) {
            $info   = JText::_('COM_SOCIALCOMMUNITY_FILEINFO_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP Intl
        $title = JText::_('COM_SOCIALCOMMUNITY_PHPINTL');
        $info  = '';
        if (!extension_loaded('intl')) {
            $info   = JText::_('COM_SOCIALCOMMUNITY_PHPINTL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification PHP Version.
        $title = JText::_('COM_SOCIALCOMMUNITY_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_SOCIALCOMMUNITY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title = JText::_('COM_SOCIALCOMMUNITY_MYSQL_VERSION');
        $info  = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_SOCIALCOMMUNITY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed Prism Library
        $info  = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_SOCIALCOMMUNITY_PRISM_LIBRARY');
            $info   = JText::_('COM_SOCIALCOMMUNITY_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion   = new Prism\Version();
            $text           = JText::sprintf('COM_SOCIALCOMMUNITY_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Socialcommunity\\Version')) {
                $componentVersion = new Socialcommunity\Version();
                $title            = JText::sprintf('COM_SOCIALCOMMUNITY_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_SOCIALCOMMUNITY_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_SOCIALCOMMUNITY_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        SocialCommunityInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        SocialCommunityInstallHelper::addRowHeading(JText::_('COM_SOCIALCOMMUNITY_INSTALLED_EXTENSIONS'));

        // SocialCommunity Library
        $result = array('type' => 'success', 'text' => JText::_('COM_SOCIALCOMMUNITY_INSTALLED'));
        SocialCommunityInstallHelper::addRow(JText::_('COM_SOCIALCOMMUNITY_SOCIALCOMMUNITY_LIBRARY'), $result, JText::_('COM_SOCIALCOMMUNITY_LIBRARY'));

        // User - Social Community New User
        $result = array('type' => 'success', 'text' => JText::_('COM_SOCIALCOMMUNITY_INSTALLED'));
        SocialCommunityInstallHelper::addRow(JText::_('COM_SOCIALCOMMUNITY_USER_SOCIALCOMMUNITY_USER'), $result, JText::_('COM_SOCIALCOMMUNITY_PLUGIN'));

        // End table with the information
        SocialCommunityInstallHelper::endTable();

        echo JText::sprintf('COM_SOCIALCOMMUNITY_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_socialcommunity'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_SOCIALCOMMUNITY_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Socialcommunity\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Socialcommunity\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_SOCIALCOMMUNITY_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }

        // Create profiles if orphans exist.
        Socialcommunity\Profile\Helper::createProfiles();
    }
}
