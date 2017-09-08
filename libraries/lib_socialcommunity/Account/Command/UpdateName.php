<?php
/**
 * @package      Socialcommunity\Account
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Account\Command;

use Prism\Command\Command;
use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;
use Socialcommunity\Account\Command\Gateway\UpdateNameGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank profile.
 *
 * @package      Socialcommunity\Account
 * @subpackage   Command
 */
class UpdateName implements Command
{
    /**
     * @var UpdateNameGateway
     */
    protected $gateway;

    /**
     * @var BasicRequest
     */
    protected $request;

    /**
     * UpdateAmount constructor.
     *
     * @param BasicRequest $request
     */
    public function __construct(BasicRequest $request)
    {
        $this->request  = $request;
    }

    /**
     * @param UpdateNameGateway $gateway
     *
     * @return self
     */
    public function setGateway(UpdateNameGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->update($this->request);
    }
}
