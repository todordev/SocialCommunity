<?php
/**
 * @package      Socialcommunity\Notification\Service
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Service\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Joomla\Utilities\ArrayHelper;
use Socialcommunity\Notification\Service\Gateway\CounterGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Notification\Service
 * @subpackage   Gateway
 */
class Counter extends JoomlaDatabase implements CounterGateway
{
    /**
     * Return the number of user notifications.
     *
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return int
     */
    public function getNotificationsNumber(array $options = array())
    {
        $userId  = ArrayHelper::getValue($options, 'user_id', 0, 'int');

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_notifications', 'a'))
            ->where('a.user_id = ' . (int)$userId);

        $status  = ArrayHelper::getValue($options, 'status');
        if ($status === null) { // Count read and not read.
            $query->where('a.status IN (0,1)');
        } else { // count one from both - read or not read.
            $query->where('a.status = ' .(int)$status);
        }

        $this->db->setQuery($query, 0, 1);

        return (int)$this->db->loadResult();
    }
}
