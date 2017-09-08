<?php
/**
 * @package      Socialcommunity
 * @subpackage   Location
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Location;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Location\Gateway\LocationGateway;

/**
 * This class provides functionality that manage the persistence of the account objects.
 *
 * @package      Socialcommunity
 * @subpackage   Location
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var LocationGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Location\Mapper($gateway);
     * </code>
     *
     * @param LocationGateway $gateway
     */
    public function __construct(LocationGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Location\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return LocationGateway
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
     * $gateway  = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Location\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Location\Location, $data);
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
        return new Location;
    }

    protected function insertObject(Entity $object)
    {
        // @todo Do insertObject method in the mapper.
    }

    protected function updateObject(Entity $object)
    {
        // @todo Do updateObject method in the mapper.
    }

    protected function deleteObject(Entity $object)
    {
        // @todo Do deleteObject method in the mapper.
    }
}
