<?php
/**
 * @package      SocialCommunity
 * @subpackage   Social
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage social profiles.
 *
 * @package      SocialCommunity
 * @subpackage   Social
 */
class SocialProfiles extends Collection
{
    /**
     * Load the data about social profiles of user from the database.
     *
     * <code>
     * $options = array(
     *     "user_id" => 1
     * );
     *
     * $socialProfiles   = new Socialcommunity\Profile\SocialProfiles(JFactory::getDbo());
     * $socialProfiles->load($options);
     *
     * foreach ($socialProfiles as $profile) {
     *    echo $profile["type"];
     *    echo $profile["alias"];
     * }
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $userId = (!array_key_exists('user_id', $options)) ? 0 : $options['user_id'];
        
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.alias, a.type')
            ->from($this->db->quoteName('#__itpsc_socialprofiles', 'a'))
            ->where('a.user_id = ' . (int)$userId);

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Return a social profile alias by type.
     *
     * <code>
     * $options = array(
     *     "user_id" => 1
     * );
     *
     * $socialProfiles   = new Socialcommunity\Profile\SocialProfiles(JFactory::getDbo());
     * $socialProfiles->load($options);
     *
     * $socialProfiles->getAlias("twitter");
     * </code>
     *
     * @param string $type This is the name of the social service - facebook, linkedin, twitter.
     *
     * @return NULL|string
     */
    public function getAlias($type)
    {
        foreach ($this->items as $item) {
            if (strcmp($type, $item['type']) === 0) {
                return $item['alias'];
            }
        }

        return null;
    }

    /**
     * Check for existing profiles.
     *
     * <code>
     * $options = array(
     *     "user_id" => 1
     * );
     *
     * $socialProfiles   = new Socialcommunity\Profile\SocialProfiles(JFactory::getDbo());
     * $socialProfiles->load($options);
     *
     * if ($socialProfiles->hasProfiles()) {
     * ....
     * }
     * </code>
     *
     * @return bool
     */
    public function hasProfiles()
    {
        return (bool)count($this->items);
    }
}
