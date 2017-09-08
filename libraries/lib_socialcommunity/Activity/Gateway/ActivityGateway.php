<?php
/**
 * @package         Socialcommunity\Activity
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Activity\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Activity\Activity;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Activity
 * @subpackage      Gateway
 */
interface ActivityGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Activity $object
     *
     * @return mixed
     */
    public function insertObject(Activity $object);

    /**
     * Update a record in database.
     *
     * @param Activity $object
     *
     * @return mixed
     */
    public function updateObject(Activity $object);
}
