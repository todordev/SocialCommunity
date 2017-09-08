<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Helper;

use Socialcommunity\Validator;
use Prism\Utilities\StringHelper;

/**
 * Helper used in Joomla business logic.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Helper
 */
abstract class JoomlaHelper
{
    /**
     * Prepare safe profile alias.
     *
     * @param string $alias
     * @param int $userId
     *
     * @return string
     * @throws \RuntimeException
     */
    public static function generateAlias($alias, $userId = 0)
    {
        $alias  = StringHelper::stringUrlSafe($alias);

        // Check for valid alias.
        $aliasValidator = new Validator\Profile\Alias($alias, $userId);
        $aliasValidator->setGateway(new Validator\Profile\Gateway\Joomla\Alias(\JFactory::getDbo()));
        if (!$aliasValidator->isValid()) {
            if (!$alias) {
                $alias = StringHelper::generateRandomString(16);
            } else {
                $alias .=  (string)mt_rand(10, 1000);
            }
        }

        return $alias;
    }
}
