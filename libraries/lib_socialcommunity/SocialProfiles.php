<?php
/**
 * @package      SocialCommunity
 * @subpackage   Social
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace SocialCommunity;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage social profiles.
 *
 * @package      SocialCommunity
 * @subpackage   Social
 */
class SocialProfiles extends ArrayObject
{
    /**
     * Load the data about social profiles of user from the database.
     *
     * <code>
     * $options = array(
     *     "id" => 1
     * );
     *
     * $socialProfiles   = new SocialCommunity\SocialProfiles(JFactory::getDbo());
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
    public function load($options = array())
    {
        $id = (!isset($options)) ? 0 : $options["id"];
        
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.alias, a.type")
            ->from($this->db->quoteName("#__itpsc_socialprofiles", "a"))
            ->where("a.user_id = " . (int)$id);

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Return a social profile alias by type.
     *
     * <code>
     * $options = array(
     *     "id" => 1
     * );
     *
     * $socialProfiles   = new SocialCommunity\SocialProfiles(JFactory::getDbo());
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
            if (strcmp($type, $item["alias"]) == 0) {
                return $item["alias"];
            }
        }

        return null;
    }
}
