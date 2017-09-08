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
 * Socialcommunity HTML Helper
 *
 * @package        Socialcommunity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlSocialcommunity
{
    /**
     * Prepare
     * @param string $dateTime
     * @param string $tz
     *
     * @return string
     */
    public static function created($dateTime, $tz = null)
    {
        $today      = Carbon\Carbon::now($tz);
        $createdOn  = Carbon\Carbon::parse($dateTime, $tz);

        $difference = (int)$today->diffInSeconds($createdOn);
        $difference = ($difference) ?: 1;
        if ($difference < 60) {
            return JText::sprintf('COM_SOCIALCOMMUNITY_SECONDS_AGO_D', $difference);
        }

        $difference = $today->diffInMinutes($createdOn);
        if ($difference <= 60) {
            return JText::sprintf('COM_SOCIALCOMMUNITY_MINUTES_AGO_D', $difference);
        }

        $difference = $today->diffInHours($createdOn);
        if ($difference <= 24) {
            return JText::sprintf('COM_SOCIALCOMMUNITY_HOURS_AGO_D', $difference);
        }

        $difference = $today->diffInDays($createdOn);
        if ($difference <= 31) {
            return JText::sprintf('COM_SOCIALCOMMUNITY_DAYS_AGO_D', $difference);
        }

        $difference = $today->diffInMonths($createdOn);
        if ($difference <= 12) {
            return JText::sprintf('COM_SOCIALCOMMUNITY_MONTHS_AGO_D', $difference);
        }

        $difference = $today->diffInYears($createdOn);
        return JText::sprintf('COM_SOCIALCOMMUNITY_YEARS_AGO_D', $difference);
    }
}
