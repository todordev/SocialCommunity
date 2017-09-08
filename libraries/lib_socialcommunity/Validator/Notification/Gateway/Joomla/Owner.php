<?php
/**
 * @package      Socialcommunity\Validator\Notification\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Notification\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Validator\Notification\Gateway\OwnerGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Validator\Notification\Gateway
 * @subpackage   Joomla
 */
class Owner extends JoomlaDatabase implements OwnerGateway
{
    /**
     * Check if notification belongs to a user.
     *
     * @param int $notificationId
     * @param int $userId
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function isOwner($notificationId, $userId)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_notifications', 'a'))
            ->where('a.id = ' . (int)$notificationId)
            ->where('a.user_id = ' . (int)$userId);

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }
}
