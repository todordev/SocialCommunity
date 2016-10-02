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

class SocialCommunityModelContact extends JModelAdmin
{
    protected $item;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
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

            $userId = (int)JFactory::getUser()->get('id');
            $data   = $this->getItem($userId);

            if (!empty($data['location_id'])) {
                $location = new Socialcommunity\Location\Location(JFactory::getDbo());
                $location->load(array('id' => $data['location_id']));

                $locationName = $location->getName(Socialcommunity\Constants::INCLUDE_COUNTRY_CODE);
                if ($locationName) {
                    $data['location_preview'] = $locationName;
                }
            }

            $secretKey       = $app->get('secret');
            
            $data['phone']   = ($data['phone'] !== null) ? Defuse\Crypto\Crypto::decrypt($data['phone'], $secretKey) : null;
            $data['address'] = ($data['address'] !== null) ? Defuse\Crypto\Crypto::decrypt($data['address'], $secretKey) : null;
        }

        return $data;
    }

    /**
     * Method to get an object.
     *
     * @param    int  $pk  The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $pk = (int)$pk;

        if ($this->item === null and $pk > 0) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('a.phone, a.address, a.location_id, a.country_id, a.website, a.phone, a.address')
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->where('a.user_id = ' . (int)$pk);

            $db->setQuery($query, 0, 1);
            $this->item    = (array)$db->loadAssoc();
        }

        return $this->item;
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
        $userId     = Joomla\Utilities\ArrayHelper::getValue($data, 'user_id', 0, 'int');
        $phone      = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'phone'));
        $address    = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'address'));
        $website    = JString::trim(Joomla\Utilities\ArrayHelper::getValue($data, 'website'));
        $countryId  = Joomla\Utilities\ArrayHelper::getValue($data, 'country_id', 0, 'int');
        $locationId = Joomla\Utilities\ArrayHelper::getValue($data, 'location_id', 0, 'int');

        $secretKey  = JFactory::getApplication()->get('secret');
        $db         = $this->getDbo();

        $phone   = (!$phone)   ? 'NULL' : $db->quote(Defuse\Crypto\Crypto::encrypt($phone, $secretKey));
        $address = (!$address) ? 'NULL' : $db->quote(Defuse\Crypto\Crypto::encrypt($address, $secretKey));
        $website = (!$website) ? 'NULL' : $db->quote($website);

        $query = $db->getQuery(true);
        $query
            ->update($db->quoteName('#__itpsc_profiles'))
            ->set($db->quoteName('location_id') . '=' . (int)$locationId)
            ->set($db->quoteName('country_id') . '=' . (int)$countryId)
            ->set($db->quoteName('website') . '=' . $website)
            ->set($db->quoteName('phone') . ' = ' . $phone)
            ->set($db->quoteName('address') . ' = ' . $address)
            ->where($db->quoteName('user_id') . ' = ' . (int)$userId);

        $db->setQuery($query);
        $db->execute();

        return $db->insertid();
    }
}
