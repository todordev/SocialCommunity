<?php
/**
 * @package      Socialcommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Helper;

use Socialcommunity\Validator;
use Joomla\Utilities\ArrayHelper;
use Prism\Validator\Date as DateValidator;

/**
 * This is a class that provides helper methods.
 *
 * @package      Socialcommunity
 * @subpackage   Profiles
 */
abstract class Helper
{
    public static function prepareBirthday(array $data)
    {
        $birthdayDay   = ArrayHelper::getValue($data, 'day', 0, 'int');
        $birthdayMonth = ArrayHelper::getValue($data, 'month', 0, 'int');
        $birthdayYear  = ArrayHelper::getValue($data, 'year', 0, 'int');

        if (!$birthdayDay) {
            $birthdayDay = '00';
        }
        if (!$birthdayMonth) {
            $birthdayMonth = '00';
        }
        if (!$birthdayYear) {
            $birthdayYear = '0000';
        }

        $birthday = $birthdayYear . '-' . $birthdayMonth . '-' . $birthdayDay;

        $date = new DateValidator($birthday);
        if (!$date->isValid()) {
            $birthday = null;
        }

        return $birthday;
    }
}
