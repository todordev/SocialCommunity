<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Profile\Command\Request\Basic as BasicRequest;
use Socialcommunity\Profile\Command\Gateway\StoreBasicGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 */
class StoreBasic extends JoomlaDatabase implements StoreBasicGateway
{
    /**
     * Store basic information.
     *
     * @param BasicRequest  $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function store(BasicRequest $request)
    {
        $bio      = !$request->getBio() ? 'NULL' : $this->db->quote($request->getBio());

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($request->getName()))
            ->set($this->db->quoteName('bio') . '=' . $bio)
            ->set($this->db->quoteName('birthday') . '=' . $this->db->quote((string)$request->getBirthday()))
            ->set($this->db->quoteName('gender') . '=' . $this->db->quote($request->getGender()))
            ->where($this->db->quoteName('user_id') . '=' . (int)$request->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
