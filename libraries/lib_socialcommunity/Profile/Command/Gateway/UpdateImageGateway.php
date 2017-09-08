<?php
/**
 * @package         Socialcommunity\Profile\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway;

use Socialcommunity\Value\Profile\Image;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Profile\Command
 * @subpackage      Gateway
 */
interface UpdateImageGateway
{
    /**
     * Update profile image.
     *
     * @param Image $profileImage
     */
    public function update(Image $profileImage);
}
