<?php
/**
 * @package         Socialcommunity\Profile
 * @subpackage      Command\Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway;

use Socialcommunity\Profile\Command\Request\Contact as ContactRequest;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile
 * @subpackage      Command\Gateway
 */
interface StoreContactGateway
{
    /**
     * Store basic information of the profile.
     *
     * @param ContactRequest $contact
     */
    public function store(ContactRequest $contact);
}
