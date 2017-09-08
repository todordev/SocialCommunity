<?php
/**
 * @package      Socialcommunity\Socialprofile
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Command;

use Prism\Command\Command;
use Socialcommunity\Socialprofile\Command\Gateway\RemoveAccessTokenGateway;

/**
 * Remove access token.
 *
 * @package      Socialcommunity\Socialprofile
 * @subpackage   Command
 */
class RemoveAccessToken implements Command
{
    /**
     * @var RemoveAccessTokenGateway
     */
    protected $gateway;

    protected $userId;
    protected $service;

    /**
     * Initialize the object.
     *
     * @param int $userId
     * @param string $service
     */
    public function __construct($userId, $service)
    {
        $this->userId = $userId;
        $this->service = $service;
    }

    /**
     * @param RemoveAccessTokenGateway $gateway
     *
     * @return self
     */
    public function setGateway(RemoveAccessTokenGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->remove($this->userId, $this->service);
    }
}
