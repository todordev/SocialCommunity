<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

// no direct access
defined('_JEXEC') or die;

class SocialcommunityModelContact extends JModelAdmin
{
    protected $item;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialcommunityTableProfile|bool  A database object
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialcommunityTable', $config = array())
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
     * @return    JForm|bool    A JForm object on success, false on failure
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
     * @throws \Exception
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
                // Prepare conditions.
                $conditionLocationId = new Condition(['column' => 'id', 'value' => $data['location_id'], 'operator' => '=', 'table' => 'a']);
                $conditions          = new Conditions();
                $conditions->addCondition($conditionLocationId);

                // Prepare database request.
                $databaseRequest = new Request();
                $databaseRequest->setConditions($conditions);

                $mapper     = new Socialcommunity\Location\Mapper(new \Socialcommunity\Location\Gateway\JoomlaGateway(JFactory::getDbo()));
                $repository = new Socialcommunity\Location\Repository($mapper);
                $location   = $repository->fetch($databaseRequest);

                $locationName = $location->getName() . ' [' . $location->getCountryCode() . ']';
                if ($locationName) {
                    $data['location_preview'] = $locationName;
                }
            }

            // Decrypt phone and address.
            if (count($data) > 0 and $data['secret_key']) {
                $password = $app->get('secret') . $userId;
                $cryptor  = new \Socialcommunity\Profile\Contact\Cryptor($data['secret_key'], $password);

                $contact = new \Socialcommunity\Profile\Contact\Contact();
                $contact->setPhone($data['phone']);
                $contact->setAddress($data['address']);

                $contact = $cryptor->decrypt($contact);

                $data['phone']   = $contact->getPhone();
                $data['address'] = $contact->getAddress();
            }
        }

        return $data;
    }

    /**
     * Method to get an object.
     *
     * @param    int $pk The id of the object to get.
     *
     * @return  mixed    Object on success, false on failure.
     * @throws \RuntimeException
     */
    public function getItem($pk = null)
    {
        $pk = (int)$pk;

        if ($this->item === null and $pk > 0) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select(
                    'a.location_id, a.country_code, a.website, ' .
                    'b.phone, b.address, b.secret_key'
                )
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->leftJoin($db->quoteName('#__itpsc_profilecontacts', 'b') . ' ON a.user_id = b.user_id')
                ->where('a.user_id = ' . (int)$pk);

            $db->setQuery($query, 0, 1);
            $this->item = (array)$db->loadAssoc();
        }

        return $this->item;
    }
}
