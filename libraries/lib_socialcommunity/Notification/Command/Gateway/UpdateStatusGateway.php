<?php
/**
 * @package         Socialcommunity\Notification\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Command\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Notification\Command
 * @subpackage      Gateway
 */
interface UpdateStatusGateway
{
    /**
     * Update notification status.
     *
     * @param int $id
     * @param int $status
     */
    public function update($id, $status);
}
