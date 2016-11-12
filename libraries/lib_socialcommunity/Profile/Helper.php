<?php
/**
 * @package      SocialCommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
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
        $alias  = StringHelper::stringUrlSafe($alias);

        // Check for valid alias.
        $aliasValidator = new Validator\Profile\Alias(\JFactory::getDbo(), $alias, $userId);
        if (!$aliasValidator->isValid()) {
            if (!$alias) {
                $alias = StringHelper::generateRandomString(16);
            } else {
                $alias .=  (string)mt_rand(10, 1000);
            }
        }

        return $alias;
    }

    /**
     * Create user profiles of the orphan user records.
     *
     * @throws \RuntimeException
     */
    public static function createProfiles()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from($db->quoteName('#__users', 'a'))
            ->leftJoin($db->quoteName('#__itpsc_profiles', 'b') . ' ON a.id = b.user_id')
            ->where('b.user_id IS NULL');

        $db->setQuery($query);
        $results = (array)$db->loadAssocList();

        foreach ($results as $result) {
            $profile = new Profile($db);

            $alias  = self::safeAlias($result['name']);

            $profile->setUserId($result['id']);
            $profile->setName($result['name']);
            $profile->setAlias($alias);

            $profile->store();
        }
    }
}
