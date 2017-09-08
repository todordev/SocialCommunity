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
use Prism\Database\Request\Request;
use Socialcommunity\Post\Gateway\PostGateway;

/**
 * This class provides a glue between persistence layer and profile object.
 *
 * @package      Socialcommunity
 * @subpackage   Post
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
     * @var PostGateway
     */
    protected $gateway;

    /**
     * Repository constructor.
     *
     * <code>
     * $profileId  = 1;
     *
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
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
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
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
     * @return Post
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
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
     *
     * $profile     = $repository->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Post
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
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
     *
     * $profiles    = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Posts
     */
    public function fetchCollection(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($request);
        if ($this->collection === null) {
            $this->collection = new Posts;
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
     *      'user_id' => 1,
     *      'content' => '....',
     *      'created_at' => '2012-12-12',
     *  );
     *
     * $post    = new Socialcommunity\Post\Post;
     * $post->bind($data);
     *
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
     *
     * $repository->store($post);
     * </code>
     *
     * @param Post $post
     */
    public function store(Post $post)
    {
        $this->mapper->save($post);
    }

    /**
     * Remove a record from database.
     *
     * <code>
     * $id = 1;
     *
     * $post    = new Socialcommunity\Post\Post;
     * $post->setId($id);
     *
     * $gateway     = new Socialcommunity\Post\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Socialcommunity\Post\Mapper($gateway);
     * $repository  = new Socialcommunity\Post\Repository($mapper);
     *
     * $repository->delete($post);
     * </code>
     *
     * @param Post $post
     */
    public function delete(Post $post)
    {
        $this->mapper->delete($post);
    }
}
