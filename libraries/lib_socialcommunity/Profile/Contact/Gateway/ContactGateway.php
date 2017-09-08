<?php
/**
 * @package         Socialcommunity\Profile
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Profile\Contact\Contact;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile
 * @subpackage      Gateway
 */
interface ContactGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Contact $object
     *
     * @return mixed
     */
    public function insertObject(Contact $object);

    /**
     * Update a record in database.
     *
     * @param Contact $object
     *
     * @return mixed
     */
    public function updateObject(Contact $object);
}
