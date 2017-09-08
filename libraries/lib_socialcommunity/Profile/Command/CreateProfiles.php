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
use Socialcommunity\Profile\Command\Gateway\CreateProfilesGateway;

/**
 * Command that creates user profiles.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
class CreateProfiles implements Command
{
    /**
     * @var CreateProfilesGateway
     */
    protected $gateway;

    /**
     * <code>
     * $userId = 1;
     *
     * $command = new CreateProfiles($userId);
     * $command->setGateway(new JoomlaCreateProfilesGateway(/JFactory::getDbo()));
     * </code>
     *
     * @param CreateProfilesGateway $gateway
     *
     * @return self
     */
    public function setGateway(CreateProfilesGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * <code>
     * $userId = 1;
     *
     * $command = new CreateProfiles($userId);
     * $command->setGateway(new JoomlaCreateProfilesGateway(/JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->create();
    }
}
