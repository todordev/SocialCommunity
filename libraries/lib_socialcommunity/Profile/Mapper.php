<?php
/**
 * @package      Socialcommunity
 * @subpackage   Profile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Profile\Gateway\ProfileGateway;

/**
 * This class provides functionality that manage the persistence of the account objects.
 *
 * @package      Socialcommunity
 * @subpackage   Profile
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var ProfileGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Profile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Profile\Mapper($gateway);
     * </code>
     *
     * @param ProfileGateway $gateway
     */
    public function __construct(ProfileGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Profile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Profile\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return ProfileGateway
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
     * $gateway  = new Socialcommunity\Profile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Profile\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Profile\Profile, $data);
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
        return new Profile;
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
