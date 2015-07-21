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
 * SocialCommunity HTML Helper
 *
 * @package        SocialCommunity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlSocialCommunity
{
    /**
     * Display a link to user social profile.
     *
     * @param array   $socialProfiles
     * @param object  $userProfile An url to the task
     * @param string  $class CSS class name.
     *
     * @return string
     */
    public static function socialprofiles($socialProfiles, $userProfile, $class = "")
    {
        $html = array();

        foreach ($socialProfiles as $profile) {

            switch ($profile["type"]) {

                case "facebook":

                    $url = "https://facebook.com/" . htmlspecialchars($profile["alias"], ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "Facebook", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));

                    $html[] = '<a href="' . $url . '" class="sc-socialprofile-facebook ' . $class . '" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/facebook_32x32.png" alt="' . $alt . '" width="32" height="32" />';
                    $html[] = '</a>';
                    break;

                case "twitter":

                    $url = "https://twitter.com/" . htmlspecialchars($profile["alias"], ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "Twitter", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));

                    $html[] = '<a href="' . $url . '" class="sc-socialprofile-twitter ' . $class . '" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/twitter_32x32.png" alt="' . $alt . '" width="32" height="32" />';
                    $html[] = '</a>';
                    break;

                case "linkedin":

                    $url = "https://linkedin.com/in/" . htmlspecialchars($profile["alias"], ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "LinkedIn", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));

                    $html[] = '<a href="' . $url . '" class="sc-socialprofile-linkedin ' . $class . '" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/linkedin_32x32.png" alt="' . $alt . '" width="32" height="32" />';
                    $html[] = '</a>';
                    break;

            }
        }

        return implode("", $html);
    }

    public static function phone($phone)
    {
        $html = array();

        if (!empty($phone)) {
            $html[] = JText::_("COM_SOCIALCOMMUNITY_PHONE");

            $html[] = ":";
            $html[] = '<span itemprop="telephone">';
            $html[] = htmlspecialchars($phone, ENT_QUOTES, "utf-8");
            $html[] = '</span>';
        }

        return implode("\n", $html);
    }

    public static function address($address, $location, $country)
    {
        $html = array();

        if (!empty($address) or !empty($location) or !empty($country)) {

            $html[] = '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

            if (!empty($address)) {
                $html[] = '<span itemprop="streetAddress" class="contact-info">';
                $html[] = htmlspecialchars($address, ENT_QUOTES, "utf-8");
                $html[] = '</span>';
            }

            if (!empty($location)) {
                $html[] = '<span itemprop="addressLocality" class="contact-info">';
                $html[] = htmlspecialchars($location, ENT_QUOTES, "utf-8");
                $html[] = '</span>';
            }

            if (!empty($country)) {
                $html[] = '<span itemprop="addressCountry" class="contact-info">';
                $html[] = htmlspecialchars($country, ENT_QUOTES, "utf-8");
                $html[] = '</span>';
            }

            $html[] = '</div>';
        }

        return implode("\n", $html);
    }
}
