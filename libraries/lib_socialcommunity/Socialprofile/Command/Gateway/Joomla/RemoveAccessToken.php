<?php
/**
 * @package      Socialcommunity\Socialprofile\Command
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Socialprofile\Command\Gateway\RemoveAccessTokenGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Socialprofile\Command
 * @subpackage   Gateway
 */
class RemoveAccessToken extends JoomlaDatabase implements RemoveAccessTokenGateway
{
    /**
     * Remove access token.
     *
     * @param int $userId
     * @param string $service
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function remove($userId, $service)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_socialprofiles'))
            ->set($this->db->quoteName('access_token') .'= NULL')
            ->set($this->db->quoteName('expires_at') .'= NULL')
            ->set($this->db->quoteName('secret_key') .'= NULL')
            ->set($this->db->quoteName('link') .'= NULL')
            ->set($this->db->quoteName('image_square') .'= NULL')
            ->set($this->db->quoteName('service_user_id') .'='. 0)
            ->where($this->db->quoteName('user_id') .'='. (int)$userId)
            ->where($this->db->quoteName('service') .'='. $this->db->quote($service));

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
