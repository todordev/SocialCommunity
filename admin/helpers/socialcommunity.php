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

class SocialCommunityHelper
{
    static $extension = "com_socialcommunity";

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_PROFILES'),
            'index.php?option=' . self::$extension . '&view=profiles',
            $vName == 'profiles'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_LOCATIONS'),
            'index.php?option=' . self::$extension . '&view=locations',
            $vName == 'locations'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_COUNTRIES'),
            'index.php?option=' . self::$extension . '&view=countries',
            $vName == 'countries'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=socialcommunity',
            $vName == 'plugins'
        );
    }
}
