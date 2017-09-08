<?php
/**
 * @package         Socialcommunity\Post
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Post\Post;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Post
 * @subpackage      Gateway
 */
interface PostGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Post $object
     *
     * @return mixed
     */
    public function insertObject(Post $object);

    /**
     * Update a record in database.
     *
     * @param Post $object
     *
     * @return mixed
     */
    public function updateObject(Post $object);

    /**
     * Delete a record from database.
     *
     * @param Post $object
     *
     * @return mixed
     */
    public function deleteObject(Post $object);
}
