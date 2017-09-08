<?php
/**
 * @package      Socialcommunity\Profile\Command\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Profile\Command\Gateway\CreateProfileGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile\Command\Gateway
 * @subpackage   Joomla
 */
final class CreateProfile extends JoomlaDatabase implements CreateProfileGateway
{
    /**
     * Create a profile.
     *
     * @param int $userId
     * @param string $name
     * @param string $alias
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function create($userId, $name, $alias)
    {
        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$userId)
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($name))
            ->set($this->db->quoteName('alias') . '=' . $this->db->quote($alias));

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
