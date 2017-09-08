<?php
/**
 * @package         Socialcommunity\Validator\Profile
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Profile\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Validator\Profile
 * @subpackage      Gateway
 */
interface AliasGateway
{
    /**
     * Check if alias exists.
     *
     * @param string $alias
     * @param int $userId
     *
     * @return bool
     */
    public function isExists($alias, $userId = 0);
}
