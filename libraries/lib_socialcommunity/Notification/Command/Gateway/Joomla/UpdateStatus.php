<?php
/**
 * @package      Socialcommunity\Notification\Command\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Notification\Command\Gateway\UpdateStatusGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Notification\Command\Gateway
 * @subpackage   Joomla
 */
class UpdateStatus extends JoomlaDatabase implements UpdateStatusGateway
{
    /**
     * Update the notification status.
     *
     * @param int  $id
     * @param int  $status
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update($id, $status)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_notifications'))
            ->set($this->db->quoteName('status') . '=' . (int)$status)
            ->where($this->db->quoteName('id') . '=' . (int)$id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
