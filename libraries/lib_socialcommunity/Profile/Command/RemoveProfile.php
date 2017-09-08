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
use Socialcommunity\Profile\Command\Gateway\RemoveProfileGateway;
use Socialcommunity\Profile\Profile;

/**
 * Command that removes user profile.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
final class RemoveProfile implements Command
{
    /**
     * @var RemoveProfileGateway
     */
    private $gateway;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * RemoveProfile constructor.
     *
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile  = $profile;
    }

    /**
     * Set database layer gateway.
     *
     * <code>
     * $profile = new Profile();
     * $profile->setId(1);
     *
     * $command = new RemoveProfile($profile);
     * $command->setGateway(new Gateway\Joomla\CreateProfile(\JFactory::getDbo()));
     * </code>
     *
     * @param RemoveProfileGateway $gateway
     *
     * @return self
     */
    public function setGateway(RemoveProfileGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Handle the command.
     *
     * <code>
     * $profile = new Profile();
     * $profile->setId(1);
     *
     * $command = new RemoveProfile($profile);
     * $command->setGateway(new Gateway\Joomla\CreateProfile(\JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->remove($this->profile);
    }
}
