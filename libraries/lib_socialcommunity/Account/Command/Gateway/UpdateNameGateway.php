<?php
/**
 * @package         Socialcommunity\Account
 * @subpackage      Command\Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Account\Command\Gateway;

use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Account
 * @subpackage      Command\Gateway
 */
interface UpdateNameGateway
{
    /**
     * Store basic information of the profile.
     *
     * @param BasicRequest $request
     */
    public function update(BasicRequest $request);
}
