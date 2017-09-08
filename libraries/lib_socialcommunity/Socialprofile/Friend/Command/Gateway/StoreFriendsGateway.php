<?php
/**
 * @package         Socialcommunity\Socialprofile\Friend\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Friend\Command\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Socialprofile\Friend\Command
 * @subpackage      Gateway
 */
interface StoreFriendsGateway
{
    /**
     * Store the user's friends from third-party social networks.
     *
     * @param string $service
     * @param int $userId
     * @param array $friends
     */
    public function store($service, $userId, array $friends);
}
