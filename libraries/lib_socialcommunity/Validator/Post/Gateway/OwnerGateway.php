<?php
/**
 * @package         Socialcommunity\Validator\Post
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Post\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Validator\Post
 * @subpackage      Gateway
 */
interface OwnerGateway
{
    /**
     * Check post owner.
     *
     * @param int $postId
     * @param int $userId
     *
     * @return bool
     */
    public function isOwner($postId, $userId);
}
