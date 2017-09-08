<?php
/**
 * @package         Socialcommunity\Notification
 * @subpackage      Service
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Service\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile
 * @subpackage      Command\Gateway
 */
interface CounterGateway
{
    /**
     * Return the number of user notifications.
     *
     * @param array $options
     */
    public function getNotificationsNumber(array $options = array());
}
