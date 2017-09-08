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
use Prism\Database\Request\Request;
use Socialcommunity\Location\Gateway\LocationGateway;

/**
 * This class provides a glue between persistence layer and profile object.
 *
 * @package      Socialcommunity
 * @subpackage   Location
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
     * @var LocationGateway
     */
    protected $gateway;

    /**
     * Repository constructor.
     *
     * <code>
     * $profileId  = 1;
     *
     * $gateway     = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Location\Mapper($gateway);
     * $repository  = new Socialcommunity\Location\Repository($mapper);
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
     * $gateway     = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Location\Mapper($gateway);
     * $repository  = new Socialcommunity\Location\Repository($mapper);
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
     * @return Location
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
     *     'location_id' => 2
     * );
     *
     * $gateway     = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Location\Mapper($gateway);
     * $repository  = new Socialcommunity\Location\Repository($mapper);
     *
     * $profile     = $repository->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Location
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
     * $gateway     = new Socialcommunity\Location\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Location\Mapper($gateway);
     * $repository  = new Socialcommunity\Location\Repository($mapper);
     *
     * $profiles    = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Locations
     */
    public function fetchCollection(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($request);

        if ($this->collection === null) {
            $this->collection = new Locations;
        }

        $this->collection->clear();
        if ($data) {
            foreach ($data as $row) {
                $this->collection[] = $this->mapper->create($row);
            }
        }

        return $this->collection;
    }
}
