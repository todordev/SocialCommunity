<?php
/**
 * @package         Socialcommunity\Profile
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Profile\Profile;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile
 * @subpackage      Gateway
 */
interface ProfileGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Profile $object
     *
     * @return mixed
     */
    public function insertObject(Profile $object);

    /**
     * Update a record in database.
     *
     * @param Profile $object
     *
     * @return mixed
     */
    public function updateObject(Profile $object);
}
