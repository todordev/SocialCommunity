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
 * SocialCommunity HTML Backend Helper
 *
 * @package        SocialCommunity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlSocialCommunityBackend
{
    /**
     * Display an icon that show a state of profile.
     * If profile exists the profile ID will have value.
     *
     * @param integer $i
     * @param string $task
     * @param int   $profileId
     * @param array   $options
     *
     * @return string
     */
    public static function profileExists($i, $task, $profileId, $options)
    {
        $html  = array();
        $class = "";
        if (!empty($options["tooltip"])) {
            JHtml::_('behavior.tooltip');
            $class = 'class="hasTooltip"';
        }

        if (!empty($profileId)) {
            $title  = addslashes(htmlspecialchars(JText::_("COM_SOCIALCOMMUNITY_PROFILE_EXISTS"), ENT_COMPAT, 'UTF-8'));
            $html[] = '<img src="../media/com_socialcommunity/images/profile_24.png" alt="' . $title . '" title="' . $title . '" ' . $class . '/>';
        } else {
            $title  = addslashes(htmlspecialchars(JText::_("COM_SOCIALCOMMUNITY_CREATE_PROFILE"), ENT_COMPAT, 'UTF-8'));
            $html[] = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\');">';
            $html[] = '<img src="../media/com_socialcommunity/images/profile_add_24.png" alt="' . $title . '" title="' . $title . '" ' . $class . '/>';
            $html[] = '</a>';
        }

        return implode("\n", $html);
    }


    public static function boolean($value, $title = "")
    {
        $title = addslashes(htmlspecialchars(JString::trim($title), ENT_COMPAT, 'UTF-8'));

        if (!$value) { // unpublished
            $class = "unpublish";
        } else {
            $class = "ok";
        }

        if (!empty($title)) {
            $title = ' title="' . $title . '"';
        }

        $html[] = '<a class="btn btn-micro" rel="tooltip" href="javascript:void(0);" ' . $title . '">';
        $html[] = '<span class="glyphicon glyphicon-' . $class . '"></span>';
        $html[] = '</a>';

        return implode($html);
    }
}
