<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class SocialCommunityModelProfile extends JModelItem
{
    protected $item = array();

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // Visitor
        $visitorId = (int)JFactory::getUser()->get('id');
        $this->setState($this->option . '.visitor.id', $visitorId);

        // If there is no ID in the URI, load profile of the visitor.
        $userId = $app->input->getInt('id');
        if (!$userId) {
            $userId = (int)JFactory::getUser()->get('id');
        }
        $this->setState($this->option . '.target.user_id', $userId);

        $value = ((int)$userId === (int)$visitorId);
        $this->setState($this->option . '.visitor.is_owner', $value);
    }

    /**
     * Method to get an object.
     *
     * @param    int $pk   The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if (!$pk) {
            $pk = $this->getState($this->option . '.target.user_id');
        }

        $storedId = $this->getStoreId($pk);

        if (!array_key_exists($storedId, $this->item)) {

            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('a.id, a.name, a.alias, a.image, a.image_square, a.bio, a.address, a.website, a.params, a.active, a.user_id')
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->where('a.user_id = ' . (int)$pk)
                ->where('a.active = ' . (int)Prism\Constants::ACTIVE);

            $db->setQuery($query, 0, 1);
            $item = $db->loadObject();

            if ($item !== null and $item->id > 0) {
                $item->params = new Joomla\Registry\Registry($item->params);

                $item->socialProfiles = new Socialcommunity\Profile\SocialProfiles(JFactory::getDbo());
                $item->socialProfiles->load(['user_id' => $item->user_id]);
            }

            $this->item[$storedId] = $item;
        }

        return $this->item[$storedId];
    }
}
