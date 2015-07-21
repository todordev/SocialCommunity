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

class SocialCommunityModelDetails extends JModelItem
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
        $visitorId = (int)JFactory::getUser()->get("id");
        $this->setState($this->option . '.visitor.id', $visitorId);

        $userId = $app->input->getInt("id");
        if (!$userId) {
            $userId = (int)JFactory::getUser()->get("id");
        }
        $this->setState($this->option . '.profile.user_id', $userId);

        $value = ($userId == $visitorId) ? true : false;
        $this->setState($this->option . '.visitor.is_owner', $value);
    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
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
                ->select("*")
                ->from($db->quoteName("#__itpsc_profiles", "a"))
                ->where("a.id = " . (int)$id);

            $db->setQuery($query, 0, 1);
            $result = $db->loadAssoc();

            // Check published state.
            if (empty($result)) {
                return null;
            }

            $this->item[$storedId] = $result;

        }

        return $this->item[$storedId];
    }
}
