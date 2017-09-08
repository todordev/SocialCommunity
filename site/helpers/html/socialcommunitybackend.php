<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Socialcommunity HTML Backend Helper
 *
 * @package        Socialcommunity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlSocialcommunityBackend
{
    public static function boolean($value, $title = '')
    {
        $title = addslashes(htmlspecialchars(JString::trim($title), ENT_COMPAT, 'UTF-8'));

        $class = 'check-circle-0';
        if (!$value) { // unpublished
            $class = 'times-circle-0';
        }

        if ($title !== '') {
            $title = ' title="' . $title . '"';
        }

        $html[] = '<a class="btn btn-micro" rel="tooltip" href="javascript:void(0);" ' . $title . '">';
        $html[] = '<span class="fa fa-' . $class . '"></span>';
        $html[] = '</a>';

        return implode($html);
    }

    public static function socialprofiles(array $socialProfiles)
    {
        $html = array();

        foreach ($socialProfiles as $service => $profileLink) {
            switch ($service) {
                case 'facebook':
                    $html[] = '<a href="'.$profileLink.'" target="_blank">';
                    $html[] = '<img src="../media/com_socialcommunity/images/facebook_20x20.png" width="20" height="20" />';
                    $html[] = '</a>';
                    break;

                case 'twitter':
                    $html[] = '<a href="'.$profileLink.'" target="_blank">';
                    $html[] = '<img src="../media/com_socialcommunity/images/twitter_20x20.png" width="20" height="20" />';
                    $html[] = '</a>';
                    break;

                case 'googleplus':
                    $html[] = '<a href="'.$profileLink.'" target="_blank">';
                    $html[] = '<img src="../media/com_socialcommunity/images/googleplus_20x20.png" width="20" height="20" />';
                    $html[] = '</a>';
                    break;
            }
        }

        return implode($html);
    }
}
