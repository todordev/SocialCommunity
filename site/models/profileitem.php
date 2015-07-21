<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class SocialCommunityModelProfileItem extends JModelItem
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
        $visitorId = (int)JFactory::getUser()->id;
        $this->setState($this->option . '.visitor.id', $visitorId);

        // If there is no ID in the URI, load profile of the visitor
        $userId = $app->input->getInt("id");
        if (!$userId) {
            $userId = $visitorId;
        }
        $this->setState($this->option . '.profile.user_id', $userId);

        $value = ($userId == $visitorId) ? true : false;
        $this->setState($this->option . '.visitor.is_owner', $value);
    }

    /**
     * Method to get an object.
     *
     * @param    integer $id   The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        if (!$id) {
            $id = $this->getState($this->option . ".profile.user_id");
        }

        $storedId = $this->getStoreId($id);

        if (!isset($this->item[$storedId])) {

            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select(
                    "a.name, a.image, a.bio, a.address, a.phone, a.website, " .
                    "b.name as location," .
                    "c.name as country"
                )
                ->from($db->quoteName("#__itpsc_profiles", "a"))
                ->leftJoin($db->quoteName("#__itpsc_locations", "b") . " ON a.location_id = b.id")
                ->leftJoin($db->quoteName("#__itpsc_countries", "c") . " ON a.country_id  = c.id")
                ->where("a.id = " . (int)$id);

            $db->setQuery($query, 0, 1);
            $result = $db->loadObject();

            // Check published state.
            if (empty($result)) {
                return null;
            }

            $this->item[$storedId] = $result;

        }

        return $this->item[$storedId];
    }
}
