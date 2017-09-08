<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Value\Profile\Image;
use Socialcommunity\Profile\Command\Gateway\UpdateImageGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 */
class UpdateImage extends JoomlaDatabase implements UpdateImageGateway
{
    /**
     * Update profile image.
     *
     * @param Image  $profileImage
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update(Image $profileImage)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_profiles'))
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($profileImage->image))
            ->set($this->db->quoteName('image_small') . '=' . $this->db->quote($profileImage->image_small))
            ->set($this->db->quoteName('image_square') . '=' . $this->db->quote($profileImage->image_square))
            ->set($this->db->quoteName('image_icon') . '=' . $this->db->quote($profileImage->image_icon))
            ->where($this->db->quoteName('id') . '=' . (int)$profileImage->profile_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
