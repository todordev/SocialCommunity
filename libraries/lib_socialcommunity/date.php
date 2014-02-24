<?php
/**
* @package      SocialCommunity
* @subpackage   Dates
* @author       Todor Iliev
* @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing dates.
 *
 * @package      SocialCommunity
 * @subpackage   Dates
 */
class SocialCommunityDate extends JDate {

    /**
     * Validate a date.
     *
     * @param string $string
     * @return boolean
     */
    public function isValid() {
    
        $month = $this->format('m');
        $day   = $this->format('d');
        $year  = $this->format('Y');
    
        if(checkdate($month, $day, $year)) {
            return true;
        } else {
            return false;
        }
    
    }
    
}