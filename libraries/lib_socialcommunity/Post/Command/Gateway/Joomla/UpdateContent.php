<?php
/**
 * @package      Socialcommunity\Post\Command\Gateway
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post\Command\Gateway\Joomla;

use Socialcommunity\Post\Post;
use Prism\Database\JoomlaDatabase;
use Socialcommunity\Post\Command\Gateway\UpdateContentGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Post\Command\Gateway
 * @subpackage   Joomla
 */
class UpdateContent extends JoomlaDatabase implements UpdateContentGateway
{
    /**
     * Update the Post content.
     *
     * @param Post  $post
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update(Post $post)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_posts'))
            ->set($this->db->quoteName('content') . '=' . $this->db->quote($post->getContent()))
            ->where($this->db->quoteName('id') . '=' . (int)$post->getId())
            ->where($this->db->quoteName('user_id') . '=' . (int)$post->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
