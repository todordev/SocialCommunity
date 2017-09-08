<?php
/**
 * @package      Socialcommunity
 * @subpackage   Socialprofile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Socialprofile\Gateway\SocialprofileGateway;

/**
 * This class provides functionality that manage the persistence of the social profile objects.
 *
 * @package      Socialcommunity
 * @subpackage   Socialprofile
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var SocialprofileGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Socialprofile\Mapper($gateway);
     * </code>
     *
     * @param SocialprofileGateway $gateway
     */
    public function __construct(SocialprofileGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Socialprofile\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return SocialprofileGateway
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
     * $gateway  = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Socialprofile\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Socialprofile\Profile, $data);
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
        return new Socialprofile;
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
