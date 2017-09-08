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
use Socialcommunity\Profile\Command\Request\Contact as ContactRequest;
use Socialcommunity\Profile\Command\Gateway\StoreContactGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank profile.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
class StoreContact implements Command
{
    /**
     * @var StoreContactGateway
     */
    protected $gateway;

    /**
     * @var ContactRequest
     */
    protected $request;

    /**
     * Store Contact command constructor.
     *
     * @param ContactRequest $request
     */
    public function __construct(ContactRequest $request)
    {
        $this->request     = $request;
    }

    /**
     * @param StoreContactGateway $gateway
     *
     * @return self
     */
    public function setGateway(StoreContactGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->store($this->request);
    }
}
