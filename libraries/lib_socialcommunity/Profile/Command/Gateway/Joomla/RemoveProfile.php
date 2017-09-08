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
use Socialcommunity\Profile\Command\Gateway\RemoveProfileGateway;
use Socialcommunity\Profile\Profile;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile\Command\Gateway
 * @subpackage   Joomla
 */
final class RemoveProfile extends JoomlaDatabase implements RemoveProfileGateway
{
    /**
     * Remove user profile.
     *
     * @param Profile $profile
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function remove(Profile $profile)
    {
        // ### Remove social friends.

        $subQuery = $this->db->getQuery(true);
        $subQuery
            ->select('sq.service_user_id')
            ->from($this->db->quoteName('#__itpsc_socialprofiles', 'sq'))
            ->where('sq.user_id =' .(int)$profile->getUserId());


        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_socialfriends'))
            ->where($this->db->quoteName('user_id') . '= (' . (string)$subQuery .')');

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove user's social profile
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_socilaprofiles'))
            ->where($this->db->quoteName('user_id') . '=' . (int)$profile->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove contact data of the profile.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_profilecontacts'))
            ->where($this->db->quoteName('user_id') . '=' . (int)$profile->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove user posts.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_posts'))
            ->where($this->db->quoteName('user_id') . '=' . (int)$profile->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove notifications.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_notifications'))
            ->where($this->db->quoteName('user_id') . '=' . (int)$profile->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove activities.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_activities'))
            ->where($this->db->quoteName('user_id') . '=' . (int)$profile->getUserId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Remove user's profile
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_profiles'))
            ->where($this->db->quoteName('id') . '=' . (int)$profile->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
