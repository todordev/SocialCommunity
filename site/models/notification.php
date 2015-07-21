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

class SocialCommunityModelNotification extends JModelItem
{
    protected $item = array();

    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }

    /**
     * Method to get an object.
     *
     * @param    int  $id  The id of the object to get.
     * @param    int  $userId  User ID.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($id, $userId)
    {
        // If missing ID, I have to return null, because there is no item.
        if (!$id or !$userId) {
            return null;
        }

        $storedId = $this->getStoreId($id);

        if (!isset($this->item[$storedId])) {

            $this->item[$storedId] = null;

            // Get a level row instance.
            $table = JTable::getInstance('Notification', 'SocialCommunityTable');
            /** @var $table SocialCommunityTableNotification */

            $keys = array("id" => $id, "user_id" => $userId);

            // Attempt to load the row.
            if ($table->load($keys)) {

                $properties = $table->getProperties();
                $properties = JArrayHelper::toObject($properties);

                $this->item[$storedId] = $properties;
            }

        }

        return (!isset($this->item[$storedId])) ? null : $this->item[$storedId];
    }
}
