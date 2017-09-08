<?php
/**
 * @package      Socialcommunity\Validator\Profile\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Profile\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Validator\Profile\Gateway\AliasGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Validator\Profile\Gateway
 * @subpackage   Joomla
 */
class Alias extends JoomlaDatabase implements AliasGateway
{
    /**
     * Check if user alias exists.
     *
     * <code>
     * $alias = 'john';
     * $userId = 1;
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $items   = $gateway->isExists($alias, $userId);
     * </code>
     *
     * @param string $alias
     * @param int $userId
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function isExists($alias, $userId = 0)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_profiles', 'a'))
            ->where('a.alias = ' . $this->db->quote($alias));

        if ($userId > 0) {
            $query->where('a.user_id != ' . (int)$userId);
        }

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }
}
