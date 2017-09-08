<?php
/**
 * @package      Socialcommunity\Profile\Command
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Profile\Command\Request\Contact as ContactRequest;
use Socialcommunity\Profile\Command\Gateway\StoreContactGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile\Command
 * @subpackage   Gateway
 */
class StoreContact extends JoomlaDatabase implements StoreContactGateway
{
    /**
     * Store basic information.
     *
     * @param ContactRequest  $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function store(ContactRequest $request)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('location_id') . '=' . (int)$request->getLocationId())
            ->set($this->db->quoteName('country_code') . '=' . $this->db->quote($request->getCountryCode()))
            ->set($this->db->quoteName('website') . '=' . $this->db->quote($request->getWebsite()))
            ->where($this->db->quoteName('user_id') . '=' . (int)$request->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
