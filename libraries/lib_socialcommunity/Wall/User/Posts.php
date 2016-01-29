<?php
/**
 * @package         SocialCommunity
 * @subpackage      Notifications
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Socialcommunity\Wall\User;

use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing wall posts.
 *
 * @package         SocialCommunity
 * @subpackage      Wall
 */
class Posts extends Collection
{
    /**
     * Load notifications of an user.
     *
     * <code>
     * $options = array(
     *      "user_id"        => 1,
     *      "limit"          => 10,
     *      "sort_direction" => "DESC"
     * );
     *
     * $entities = new Socialcommunity\Wall\User\Posts(JFactory::getDbo());
     * $entities->load($options);
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load(array $options = array())
    {
        $userId     = ArrayHelper::getValue($options, 'user_id', 0, 'integer');

        $sortDir    = ArrayHelper::getValue($options, 'sort_direction', 'DESC');
        $sortDir    = (strcmp('DESC', $sortDir) === 0) ? 'DESC' : 'ASC';

        $limit      = ArrayHelper::getValue($options, 'limit', 10, 'int');

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.content, a.url, a.created, a.media, a.user_id, ' .
                'b.name, b.alias, b.image_square'
            )
            ->from($this->db->quoteName('#__itpsc_userwalls', 'a'))
            ->innerJoin($this->db->quoteName('#__itpsc_profiles', 'b') . ' ON a.user_id = b.user_id')
            ->where('a.user_id = ' . (int)$userId)
            ->order('a.created ' . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Count user's posts.
     *
     * <code>
     * $options = array(
     *      "user_id"        => 1
     * );
     *
     * $entities = new Socialcommunity\Wall\User\Posts(JFactory::getDbo());
     * echo $entities->getNumber($options);
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @return int
     */
    public function getNumber(array $options = array())
    {
        $userId  = ArrayHelper::getValue($options, 'user_id', 0, 'integer');
        if (!$userId) {
            return count($this->items);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_userwalls', 'a'))
            ->where('a.user_id = ' . (int)$userId);

        $this->db->setQuery($query, 0, 1);

        return (int)$this->db->loadResult();
    }
}
