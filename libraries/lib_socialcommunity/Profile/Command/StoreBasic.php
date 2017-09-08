<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command;

use Prism\Command\Command;
use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;
use Socialcommunity\Profile\Command\Gateway\StoreBasicGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank profile.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
class StoreBasic implements Command
{
    /**
     * @var StoreBasicGateway
     */
    protected $gateway;

    /**
     * @var BasicRequest
     */
    protected $request;

    /**
     * Store basic profile data command constructor.
     *
     * @param BasicRequest $request
     */
    public function __construct(BasicRequest $request)
    {
        $this->request     = $request;
    }

    /**
     * @param StoreBasicGateway $gateway
     *
     * @return self
     */
    public function setGateway(StoreBasicGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->store($this->request);
    }
}
