<?php
/**
 * @package      Socialcommunity\Notification
 * @subpackage   Validators
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Notification;

use Prism\Validator\ValidatorInterface;
use Socialcommunity\Validator\Notification\Gateway\OwnerGateway;

/**
 * This class provides functionality for validation notification owner.
 *
 * @package      Socialcommunity\Notification
 * @subpackage   Validators
 */
class Owner implements ValidatorInterface
{
    /**
     * @var OwnerGateway
     */
    protected $gateway;

    protected $notificationId;
    protected $userId;

    /**
     * Initialize the object.
     *
     * <code>
     * $notificationId = 1;
     * $userId = 2;
     *
     * $validatorOwner = new Socialcommunity\Validator\Notification\Owner($notificationId, $userId);
     * </code>
     *
     * @param int  $notificationId
     * @param int  $userId
     */
    public function __construct($notificationId, $userId)
    {
        $this->notificationId   = $notificationId;
        $this->userId           = $userId;
    }

    /**
     * Set database gateway.
     *
     * @param OwnerGateway $gateway
     */
    public function setGateway(OwnerGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Validate notification owner.
     *
     * <code>
     * $notificationId = 1;
     * $userId = 2;
     *
     * $validatorOwner = new Socialcommunity\Validator\Notification\Owner($notificationId, $userId);
     * $validatorOwner->setGateway(new Socialcommunity\Validator\Notification\Gateway\Joomla\Owner(JFactory::getDbo());
     *
     * if(!$validatorOwner->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->gateway->isOwner($this->notificationId, $this->userId);
    }
}
