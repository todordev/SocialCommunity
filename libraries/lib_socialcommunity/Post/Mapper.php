<?php
/**
 * @package      Socialcommunity
 * @subpackage   Post
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post;

use Prism\Domain;
use Prism\Domain\Entity;
use Socialcommunity\Post\Gateway\PostGateway;

/**
 * This class provides functionality that manage the persistence of the account objects.
 *
 * @package      Socialcommunity
 * @subpackage   Post
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var PostGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Post\Mapper($gateway);
     * </code>
     *
     * @param PostGateway $gateway
     */
    public function __construct(PostGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Socialcommunity\Post\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return PostGateway
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
     * $gateway  = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($profileId);
     *
     * $mapper   = new Socialcommunity\Post\Mapper($gateway);
     * $profile = $mapper->populate(new Socialcommunity\Post\Post, $data);
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
        return new Post;
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
