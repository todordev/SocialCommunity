<?php
/**
 * @package      Socialcommunity
 * @subpackage   Activity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Activity;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Activity\Gateway\ActivityGateway;

/**
 * This class provides functionality that manage the persistence of the account objects.
 *
 * @package      Socialcommunity
 * @subpackage   Activity
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var ActivityGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Activity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Activity\Mapper($gateway);
     * </code>
     *
     * @param ActivityGateway $gateway
     */
    public function __construct(ActivityGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Activity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Activity\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return ActivityGateway
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
     * $gateway  = new Socialcommunity\Activity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Activity\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Activity\Activity, $data);
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
        return new Activity;
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
        // @todo Do deleteObject method in the mapper.
    }
}
