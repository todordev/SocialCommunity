<?php
/**
 * @package      Socialcommunity\Socialprofile\Friend\Command
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Friend\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Socialprofile\Friend\Command\Gateway\StoreFriendsGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Socialprofile\Friend\Command
 * @subpackage   Gateway
 */
class StoreFriends extends JoomlaDatabase implements StoreFriendsGateway
{
    /**
     * Store the user's friends from third-party social networks.
     *
     * @param string $service
     * @param int $userId
     * @param array $friends
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function store($service, $userId, array $friends)
    {
        $allowedServices = ['googleplus', 'twitter', 'facebook'];
        if (!in_array($service, $allowedServices, true)) {
            throw new \InvalidArgumentException('Invalid social network (service).');
        }

        $friends_ = array();
        foreach ($friends as $friend) {
            $friends_[] = $friend['id'];
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.friend_id')
            ->from($this->db->quoteName('#__itpsc_socialfriends', 'a'))
            ->where('a.user_id = '. $this->db->quote($userId))
            ->where('a.service = '. $this->db->quote($service));

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList('id', 'friend_id');

        // Prepare friends for adding
        $recordsForAdding = array_diff($friends_, $results);

        // Create records.
        $newRecords = array();
        foreach ($recordsForAdding as $key => $friendId) {
            $newRecords[]  = $this->db->quote($userId) .','. $this->db->quote($friendId) .','.$this->db->quote($friends[$key]['name']).','.$this->db->quote($service);
        }

        // Add the new records.
        if (count($newRecords) > 0) {
            $query = $this->db->getQuery(true);
            $query
                ->insert($this->db->quoteName('#__itpsc_socialfriends'))
                ->columns($this->db->quoteName(['user_id', 'friend_id', 'name', 'service']))
                ->values($newRecords);

            $this->db->setQuery($query);
            $this->db->execute();
        }

        // Prepare friends for removing.
        $recordsForRemoving = array_diff($results, $friends_);
        if (count($recordsForRemoving) > 0) {
            // Prepare friend IDs.
            $recordsForRemoving = array_map(function ($value) {
                return $this->db->quote($value);
            }, $recordsForRemoving);

            // Remove the records/
            $query = $this->db->getQuery(true);
            $query
                ->delete($this->db->quoteName('#__itpsc_socialfriends'))
                ->where($this->db->quoteName('user_id') .'='. $this->db->quote($userId))
                ->where($this->db->quoteName('friend_id') .' IN ('. implode(',', $recordsForRemoving) .')')
                ->where($this->db->quoteName('service') .'='. $this->db->quote($service));

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
}
