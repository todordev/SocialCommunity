<?php
/**
 * @package      Socialcommunity\Notification
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Command;

use Prism\Command\Command;
use Socialcommunity\Notification\Command\Gateway\UpdateStatusGateway;

/**
 * Update notification status command.
 *
 * @package      Socialcommunity\Notification
 * @subpackage   Command
 */
class UpdateStatus implements Command
{
    /**
     * @var int
     */
    protected $id;
    protected $status;

    /**
     * @var UpdateStatusGateway
     */
    protected $gateway;

    /**
     * Store basic profile data command constructor.
     *
     * @param int $id
     * @param int $status
     */
    public function __construct($id, $status)
    {
        $this->id     = (int)$id;
        $this->status = (int)$status;
    }

    /**
     * @param UpdateStatusGateway $gateway
     *
     * @return self
     */
    public function setGateway(UpdateStatusGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        if (!$this->id) {
            throw new \InvalidArgumentException('It is missing notification ID.');
        }

        $this->gateway->update($this->id, $this->status);
    }
}
