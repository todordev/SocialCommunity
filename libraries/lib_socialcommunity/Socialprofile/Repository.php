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
use Prism\Database\Request\Request;
use Socialcommunity\Socialprofile\Gateway\SocialprofileGateway;

/**
 * This class provides a glue between persistence layer and profile object.
 *
 * @package      Socialcommunity
 * @subpackage   Socialprofile
 */
class Repository extends Domain\Repository implements Domain\CollectionFetcher
{
    /**
     * Collection object.
     *
     * @var Domain\Collection
     */
    protected $collection;

    /**
     * @var SocialprofileGateway
     */
    protected $gateway;

    /**
     * Repository constructor.
     *
     * <code>
     * $profileId  = 1;
     *
     * $gateway     = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Socialprofile\Mapper($gateway);
     * $repository  = new Socialcommunity\Socialprofile\Repository($mapper);
     * </code>
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper  = $mapper;
        $this->gateway = $mapper->getGateway();
    }

    /**
     * Load the data from database and return an entity.
     *
     * <code>
     * $profileId  = 1;
     *
     * $gateway     = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Socialprofile\Mapper($gateway);
     * $repository  = new Socialcommunity\Socialprofile\Repository($mapper);
     *
     * $profile     = $repository->findById($profileId);
     * </code>
     *
     * @param int $id
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Socialprofile
     */
    public function fetchById($id, Request $request = null)
    {
        if (!$id) {
            throw new \InvalidArgumentException('There is no ID.');
        }

        $data = $this->gateway->fetchById($id, $request);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'user_id' => 1,
     *     'type' => 'facebook'
     * );
     *
     * $gateway     = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Socialprofile\Mapper($gateway);
     * $repository  = new Socialcommunity\Socialprofile\Repository($mapper);
     *
     * $profile     = $repository->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Socialprofile
     */
    public function fetch(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetch($request);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $conditions = array(
     *     'ids' => array(1,2,3,4)
     * );
     *
     * $gateway     = new Socialcommunity\Socialprofile\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Socialprofile\Mapper($gateway);
     * $repository  = new Socialcommunity\Socialprofile\Repository($mapper);
     *
     * $profiles    = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Socialprofiles
     */
    public function fetchCollection(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($request);

        if ($this->collection === null) {
            $this->collection = new Socialprofiles;
        }

        $this->collection->clear();
        if ($data) {
            foreach ($data as $row) {
                $this->collection[] = $this->mapper->create($row);
            }
        }

        return $this->collection;
    }

    /**
     * Store the data from social profile object to database.
     *
     * <code>
     * $gateway     = new Socialcommunity\Socialprofile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Socialcommunity\Socialprofile\Contact\Repository($gateway);
     *
     * $repository->store($profile);
     * </code>
     *
     * @param Socialprofile  $profile
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function store(Socialprofile $profile)
    {
        if ($profile->getId()) {
            $this->gateway->updateObject($profile);
        } else {
            $this->gateway->insertObject($profile);
        }
    }
}
