<?php
/**
 * @package         Socialcommunity\Profile
 * @subpackage      Command\Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway;

use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile
 * @subpackage      Command\Gateway
 */
interface StoreBasicGateway
{
    /**
     * Store basic information of the profile.
     *
     * @param BasicRequest $request
     */
    public function store(BasicRequest $request);
}
