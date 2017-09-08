<?php
/**
 * @package         Socialcommunity\Post\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post\Command\Gateway;

use Socialcommunity\Post\Post;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Post\Command
 * @subpackage      Gateway
 */
interface UpdateContentGateway
{
    /**
     * Update Post content.
     *
     * @param Post $post
     */
    public function update(Post $post);
}
