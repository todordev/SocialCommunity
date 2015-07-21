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

class SocialCommunityModelSocial extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array $config  Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'SocialProfile', $prefix = 'SocialCommunityTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

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

        // Set user ID to state.
        $this->setState($this->getName() . ".profile.user_id", JFactory::getUser()->id);

        // Load the parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param    array   $data     An optional array of data for the form to interogate.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.social', 'social', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $data = $app->getUserState($this->option . '.profile.social', array());
        if (!$data) {
            $items = $this->getItems();

            if (!empty($items)) {
                foreach ($items as $item) {
                    $data[$item["type"]] = $item["alias"];
                }
            }
        }

        return $data;
    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItems($id = null)
    {
        $items = array();

        if (!$id) {
            $id = $this->getState($this->getName() . ".profile.user_id");
        }

        if (!empty($id)) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select("a.id, a.alias, a.type, a.user_id")
                ->from($db->quoteName("#__itpsc_socialprofiles", "a"))
                ->where("a.user_id = " . (int)$id);

            $db->setQuery($query);
            $items = $db->loadAssocList();

            if (empty($items)) {
                $items = array();
            }

        }

        return $items;
    }

    /**
     * Method to save the form data.
     *
     * @param    array    $profiles    The form data.
     *
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function save($profiles)
    {
        $userId = JFactory::getUser()->get("id");

        $allowedTypes = array("facebook", "twitter", "linkedin");

        foreach ($profiles as $key => $alias) {

            $type  = JString::trim($key);
            $alias = JString::trim($alias);

            if (!in_array($type, $allowedTypes)) {
                continue;
            }

            $keys = array(
                "user_id" => (int)$userId,
                "type"    => $type
            );

            // Load a record from the database
            $row = $this->getTable();
            $row->load($keys);

            // Remove old
            if (!empty($row->id) and !$alias) { // If there is a record but there is now alias, remove old record.

                $row->delete();

            } elseif (!$alias) { // If missing alias, continue to next social profile.

                continue;

            } else { // Add new

                if (!$row->id) {
                    $row->set("user_id", (int)$userId);
                }

                $row->set("alias", $alias);
                $row->set("type", $type);

                $row->store();

            }
        }
    }
}
