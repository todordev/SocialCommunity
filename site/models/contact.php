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

class SocialCommunityModelContact extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialCommunityTable', $config = array())
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
        $userId = JFactory::getUser()->get("id");
        $this->setState($this->getName() . ".profile.user_id", $userId);

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
        $form = $this->loadForm($this->option . '.contact', 'contact', array('control' => 'jform', 'load_data' => $loadData));
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

        $data = $app->getUserState($this->option . '.profile.contact', array());


        if (!$data) {
            $data = $this->getItem();

            if (!empty($data["location_id"])) {

                $location = new SocialCommunity\Location(JFactory::getDbo());
                $location->load(array("id" => $data["location_id"]));

                $locationName = $location->getName(SocialCommunity\Constants::INCLUDE_COUNTRY_CODE);

                if (!empty($locationName)) {
                    $data["location_preview"] = $locationName;
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
    public function getItem($id = null)
    {
        $item = null;

        if (!$id) {
            $id = $this->getState($this->getName() . ".profile.user_id");
        }

        if (!empty($id)) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select("a.phone, a.address, a.location_id, a.country_id, a.website")
                ->from($db->quoteName("#__itpsc_profiles", "a"))
                ->where("a.id = " . (int)$id);

            $db->setQuery($query, 0, 1);
            $item = $db->loadAssoc();

            if (empty($item)) {
                $item = null;
            }

        }

        return $item;
    }

    /**
     * Method to save the form data.
     *
     * @param    array    $data    The form data.
     *
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function save($data)
    {
        $id         = JFactory::getUser()->get("id");
        $phone      = JString::trim(JArrayHelper::getValue($data, "phone"));
        $address    = JString::trim(JArrayHelper::getValue($data, "address"));
        $locationId = JString::trim(JArrayHelper::getValue($data, "location_id", 0, "int"));
        $countryId  = JString::trim(JArrayHelper::getValue($data, "country_id", 0, "int"));
        $website    = JString::trim(JArrayHelper::getValue($data, "website"));

        if (empty($phone)) {
            $phone = null;
        }
        if (empty($address)) {
            $address = null;
        }
        if (empty($website)) {
            $website = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("phone", $phone);
        $row->set("address", $address);
        $row->set("location_id", $locationId);
        $row->set("country_id", $countryId);
        $row->set("website", $website);

        $row->store(true);

        return $row->get("id");
    }
}
