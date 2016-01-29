<?php
/**
 * @package      SocialCommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Socialcommunity\Profile;

use Socialcommunity\Validator;
use Prism\Utilities\StringHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing profile.
 *
 * @package      SocialCommunity
 * @subpackage   Profiles
 */
final class Helper
{
    /**
     * Prepare safe profile alias.
     *
     * @param string $alias
     * @param int $userId
     *
     * @return string
     */
    public static function safeAlias($alias, $userId = 0)
    {
        $filter = new \JFilterInput;
        $alias  = \JString::strtolower($filter->clean($alias, 'ALNUM'));

        // Check for valid alias.
        $aliasValidator = new Validator\Profile\Alias(\JFactory::getDbo(), $alias, $userId);
        if (!$aliasValidator->isValid()) {
            if (!$alias) {
                $alias = StringHelper::generateRandomString(16);
            } else {
                $alias .=  mt_rand(10, 1000);
            }
        }

        return $alias;
    }
}
