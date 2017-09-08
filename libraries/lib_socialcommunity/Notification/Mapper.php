<?php
/**
 * @package      Socialcommunity
 * @subpackage   Notification
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Notification\Gateway\NotificationGateway;

/**
 * This class provides functionality that manage the persistence of the account objects.
 *
 * @package      Socialcommunity
 * @subpackage   Notification
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var NotificationGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Notification\Mapper($gateway);
     * </code>
     *
     * @param NotificationGateway $gateway
     */
    public function __construct(NotificationGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Notification\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return NotificationGateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }
    
    /**
     * Populate an object.
     *
     * <code>
     * $profileId = 1;
     *
     * $gateway  = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Notification\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Notification\Notification, $data);
     * </code>
     *
     * @param Entity $object
     * @param array  $data
     *
     * @return Entity
     */
    public function populate(Entity $object, array $data)
    {
        $object->bind($data);

        return $object;
    }

    protected function createObject()
    {
        return new Notification;
    }

    protected function insertObject(Entity $object)
    {
        $this->gateway->insertObject($object);
    }

    protected function updateObject(Entity $object)
    {
        $this->gateway->updateObject($object);
    }

    protected function deleteObject(Entity $object)
    {
        $this->gateway->deleteObject($object);
    }
}
