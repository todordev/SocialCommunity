<?php
/**
 * @package         Socialcommunity\Validator\Notification
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Notification\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Validator\Notification
 * @subpackage      Gateway
 */
interface OwnerGateway
{
    /**
     * Check notification owner.
     *
     * @param int $notificationId
     * @param int $userId
     *
     * @return bool
     */
    public function isOwner($notificationId, $userId);
}
