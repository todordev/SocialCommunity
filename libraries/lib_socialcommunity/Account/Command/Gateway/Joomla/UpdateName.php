<?php
/**
 * @package      Socialcommunity\Account
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Account\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;
use Socialcommunity\Account\Command\Gateway\UpdateNameGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Account
 * @subpackage   Command\Gateway
 */
class UpdateName extends JoomlaDatabase implements UpdateNameGateway
{
    /**
     * Update user name in his account.
     *
     * @param BasicRequest  $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update(BasicRequest $request)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__users'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($request->getName()))
            ->where($this->db->quoteName('id') . '=' . (int)$request->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
