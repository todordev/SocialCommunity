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

class SocialCommunityModelSettings extends JModelForm
{
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

        $userId = (int)JFactory::getUser()->get('id');
        $this->setState($this->option . '.settings.user_id', $userId);
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
        $form = $this->loadForm($this->option . '.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
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

        $data = $app->getUserState($this->option . '.settings', array());
        if (!$data) {
            $data = $this->getItem();
        }

        return $data;
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
            $pk = $this->getState($this->option . '.settings.user_id');
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('a.params, a.active')
            ->from($db->quoteName('#__itpsc_profiles', 'a'))
            ->where('a.user_id = ' . (int)$pk);

        $db->setQuery($query, 0, 1);
        $result = $db->loadAssoc();

        // Prepare setting groups.
        if (!$result['params']) {
            $privacy = array();
        } else {
            $privacy = json_decode($result['params'], true);
            $privacy = array_key_exists('privacy', $privacy) ? $privacy['privacy'] : array();
        }

        $settings = array(
            'account' => array(
                'account_state' => $result['active']
            ),
            'privacy' => $privacy
        );

        return $settings;
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
        $userId          = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $accessPicture   = Joomla\Utilities\ArrayHelper::getValue($data['privacy'], 'picture', 0, 'int');
        $accessBio       = Joomla\Utilities\ArrayHelper::getValue($data['privacy'], 'bio', 0, 'int');
        $accountState    = Joomla\Utilities\ArrayHelper::getValue($data['account'], 'account_state', 0, 'int');

        // Prepare privacy settings.
        $profileParams   = new Joomla\Registry\Registry();
        $profileParams->set('privacy.picture', $accessPicture);
        $profileParams->set('privacy.bio', $accessBio);
        $profileParams = $profileParams->toString();

        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->update($db->quoteName('#__itpsc_profiles'))
            ->set($db->quoteName('params') .'='. $db->quote($profileParams))
            ->set($db->quoteName('active') .'='. (int)$accountState)
            ->where($db->quoteName('user_id') .' = '. (int)$userId);

        $db->setQuery($query);
        $db->execute();
    }
}
