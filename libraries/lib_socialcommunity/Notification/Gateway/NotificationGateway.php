<?php
/**
 * @package         Socialcommunity\Notification
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Notification\Notification;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Notification
 * @subpackage      Gateway
 */
interface NotificationGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Notification $object
     *
     * @return mixed
     */
    public function insertObject(Notification $object);

    /**
     * Update a record in database.
     *
     * @param Notification $object
     *
     * @return mixed
     */
    public function updateObject(Notification $object);
}
