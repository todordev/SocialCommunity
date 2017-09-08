<?php
/**
 * @package         Socialcommunity\Socialprofile
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Gateway;

use Prism\Domain\RichFetcher;
use Socialcommunity\Socialprofile\Socialprofile;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Socialprofile
 * @subpackage      Gateway
 */
interface SocialprofileGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Socialprofile $object
     *
     * @return mixed
     */
    public function insertObject(Socialprofile $object);

    /**
     * Update a record in database.
     *
     * @param Socialprofile $object
     *
     * @return mixed
     */
    public function updateObject(Socialprofile $object);
}
