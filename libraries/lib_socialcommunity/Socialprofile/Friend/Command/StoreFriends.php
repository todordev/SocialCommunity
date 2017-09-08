<?php
/**
 * @package      Socialcommunity\Socialprofile\Friend
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Friend\Command;

use Prism\Command\Command;
use Socialcommunity\Socialprofile\Friend\Command\Gateway\StoreFriendsGateway;

/**
 * Store user's friends.
 *
 * @package      Socialcommunity\Socialprofile\Friend
 * @subpackage   Command
 */
class StoreFriends implements Command
{
    /**
     * @var StoreFriendsGateway
     */
    protected $gateway;

    protected $service;
    protected $userId;
    protected $friends;

    /**
     * Initialize the object.
     *
     * @param string $service
     * @param int $userId
     * @param array $friends
     */
    public function __construct($service, $userId, array $friends)
    {
        $this->service = $service;
        $this->userId  = $userId;
        $this->friends = $friends;
    }

    /**
     * @param StoreFriendsGateway $gateway
     *
     * @return self
     */
    public function setGateway(StoreFriendsGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->store($this->service, $this->userId, $this->friends);
    }
}
