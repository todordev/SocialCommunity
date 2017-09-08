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
use Prism\Database\Request\Request;
use Socialcommunity\Notification\Gateway\NotificationGateway;

/**
 * This class provides a glue between persistence layer and profile object.
 *
 * @package      Socialcommunity
 * @subpackage   Notification
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
     * @var NotificationGateway
     */
    protected $gateway;

    /**
     * Repository constructor.
     *
     * <code>
     * $profileId  = 1;
     *
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
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
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
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
     * @return Notification
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
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
     *
     * $profile     = $repository->fetch($conditions);
     * </code>
     *
     * @param Request  $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Notification
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
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
     *
     * $profiles    = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param Request  $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Notifications
     */
    public function fetchCollection(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($request);

        if ($this->collection === null) {
            $this->collection = new Notifications;
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
     * Store the data in database.
     *
     * <code>
     * $data = array(
     *      'content' => '....',
     *      'created_at' => '2012-12-12',
     *      'status' => 'new',
     *      'image' => '...',
     *      'url' => '...',
     *      'target_id' => 2
     *  );
     *
     * $notification    = new Socialcommunity\Notification\Notification;
     * $notification->bind($data);
     *
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
     *
     * $repository->store($notification);
     * </code>
     *
     * @param Notification $notification
     */
    public function store(Notification $notification)
    {
        $this->mapper->save($notification);
    }

    /**
     * Remove a notification record.
     *
     * <code>
     * $id = 1;
     *
     * $notification    = new Socialcommunity\Notification\Notification;
     * $notification->setId($id);
     *
     * $gateway     = new Socialcommunity\Notification\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Notification\Mapper($gateway);
     * $repository  = new Socialcommunity\Notification\Repository($mapper);
     *
     * $repository->delete($notification);
     * </code>
     *
     * @param Notification $notification
     */
    public function delete(Notification $notification)
    {
        $this->mapper->delete($notification);
    }
}
