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
use Socialcommunity\Profile\Command\Gateway\CreateProfileGateway;

/**
 * Command that creates user profile.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
final class CreateProfile implements Command
{
    /**
     * @var CreateProfileGateway
     */
    private $gateway;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $alias;

    public function __construct($userId, $name, $alias)
    {
        $this->userId = (int)$userId;
        $this->name   = $name;
        $this->alias  = $alias;
    }

    /**
     * Set database layer gateway.
     *
     * <code>
     * $command = new CreateProfile($userId, $name, $alias);
     * $command->setGateway(new Gateway\Joomla\CreateProfile(\JFactory::getDbo()));
     * </code>
     *
     * @param CreateProfileGateway $gateway
     *
     * @return self
     */
    public function setGateway(CreateProfileGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Handle the command.
     *
     * <code>
     * $command = new CreateProfile($userId, $name, $alias);
     * $command->setGateway(new Gateway\Joomla\CreateProfile(\JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->create($this->userId, $this->name, $this->alias);
    }
}
