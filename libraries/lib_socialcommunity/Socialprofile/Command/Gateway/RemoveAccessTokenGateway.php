<?php
/**
 * @package         Socialcommunity\Socialprofile\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Command\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Socialprofile\Command
 * @subpackage      Gateway
 */
interface RemoveAccessTokenGateway
{
    /**
     * Remove access token.
     *
     * @param int $userId
     * @param string $service
     */
    public function remove($userId, $service);
}
