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
 * SocialCommunity HTML Helper
 *
 * @package		SocialCommunity
 * @subpackage	Components
 * @since		1.6
 */
abstract class JHtmlSocialCommunity {
    
    /**
     * Display a link to user social profile.
     * 
     * @param integer $value
     * @param string  $url		An url to the task
     */
    public static function socialprofiles($socialProfiles, $userProfile, $class = "") {
    
        $html = array();
        
        foreach($socialProfiles as $profile) {
    
            switch($profile->type) {
        
                case "facebook":
                    
                    $url = "https://facebook.com/".htmlspecialchars($profile->alias, ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "Facebook", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));
                    
                    $html[] = '<a href="'.$url.'" class="sc-socialprofile-facebook '.$class.'" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/facebook_32x32.png" alt="'.$alt.'" width="32" height="32" />';
                    $html[] = '</a>';
                    break;
                    
                case "twitter":
                
                    $url = "https://twitter.com/".htmlspecialchars($profile->alias, ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "Twitter", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));
                
                    $html[] = '<a href="'.$url.'" class="sc-socialprofile-twitter '.$class.'" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/twitter_32x32.png" alt="'.$alt.'" width="32" height="32" />';
                    $html[] = '</a>';
                    break;
                    
                case "linkedin":
                
                    $url = "https://linkedin.com/in/".htmlspecialchars($profile->alias, ENT_QUOTES, "UTF-8");
                    $alt = JText::sprintf("COM_SOCIALCOMMUNITY_SOCIAL_PROFILE_ALT", "LinkedIn", htmlspecialchars($userProfile->name, ENT_QUOTES, "UTF-8"));
                
                    $html[] = '<a href="'.$url.'" class="sc-socialprofile-linkedin '.$class.'" target="_blank">';
                    $html[] = '<img src="media/com_socialcommunity/images/linkedin_32x32.png" alt="'.$alt.'" width="32" height="32" />';
                    $html[] = '</a>';
                    break;
        
            }
        }
    
        return implode("\n", $html);
    }
    
}
