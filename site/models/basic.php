<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SocialCommunityModelBasic extends JModelForm
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string   $type The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialCommunityTableProfile  A database object
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
        $userId = JFactory::getUser()->get('id');
        $this->setState($this->getName() . '.profile.user_id', $userId);

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
     * @param    array   $data     An optional array of data for the form to interrogate.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.basic', 'basic', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
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

        $data = $app->getUserState($this->option . '.profile.basic', array());
        if (!$data) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
     *
     * @return    mixed   Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        $item = null;

        if (!$id) {
            $id = (int)$this->getState($this->getName() . '.profile.user_id');
        }

        if ($id > 0) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('a.name, a.bio, a.birthday, a.gender')
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->where('a.user_id = ' . (int)$id);

            $db->setQuery($query, 0, 1);
            $item = $db->loadObject();
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
        $id   = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $name = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'name'));
        $bio  = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'bio'));

        if (!$bio) {
            $bio = null;
        }

        // Prepare gender.
        $allowedGender = array('male', 'female');
        $gender        = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'gender'));
        if (!in_array($gender, $allowedGender, true)) {
            $gender = 'male';
        }

        // Prepare birthday
        $birthday = SocialCommunityHelper::prepareBirthday($data);

        // Load a record from the database
        $row = $this->getTable();
        $row->load(array('user_id' => $id));

        $row->set('name', $name);
        $row->set('bio', $bio);
        $row->set('birthday', $birthday);
        $row->set('gender', $gender);

        $row->store(true);

        // Update the name in Joomla! users table
        SocialCommunityHelper::updateName($id, $name);

        return $row->get('id');
    }
}
