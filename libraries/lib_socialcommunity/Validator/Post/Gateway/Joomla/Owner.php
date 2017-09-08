<?php
/**
 * @package      Socialcommunity\Validator\Post\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Post\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Validator\Post\Gateway\OwnerGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Validator\Post\Gateway
 * @subpackage   Joomla
 */
class Owner extends JoomlaDatabase implements OwnerGateway
{
    /**
     * Check if post belongs to a user.
     *
     * @param int $postId
     * @param int $userId
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function isOwner($postId, $userId)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_posts', 'a'))
            ->where('a.id = ' . (int)$postId)
            ->where('a.user_id = ' . (int)$userId);

        $this->db->setQuery($query, 0, 1);
        return (bool)$this->db->loadResult();
    }
}
