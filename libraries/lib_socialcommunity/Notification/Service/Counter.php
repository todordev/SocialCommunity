<?php
/**
 * @package      Socialcommunity\Notification
 * @subpackage   Service
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Service;

use Socialcommunity\Notification\Service\Gateway\CounterGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank profile.
 *
 * @package      Socialcommunity\Notification
 * @subpackage   Service
 */
class Counter
{
    /**
     * @var CounterGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * @param CounterGateway $gateway
     */
    public function __construct(CounterGateway $gateway)
    {
        $this->gateway  = $gateway;
    }

    /**
     * Count the number of user notifications.
     *
     * @param array $options
     *
     * @return int
     */
    public function getNotificationsNumber($options)
    {
        return $this->gateway->getNotificationsNumber($options);
    }
}
