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

/**
 * SocialCommunity profile controller.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerProfile extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   stdClass    The model.
     * @since    1.5
     */
    public function getModel($name = 'Contact', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @throws Exception
     *
     * @return  void
     *
     * @since   3.0
     */
    public function loadLocation()
    {
        // Get the input
        $app       = JFactory::getApplication();
        $search    = $app->input->get->get('search', '', 'string');
        $countryId = $app->input->get->getInt('country_id');

        $response = new Prism\Response\Json();

        try {
            $locations = new Socialcommunity\Location\Locations(JFactory::getDbo());
            $locations->load(array('search' => $search, 'country_id' => $countryId));

            $locationData = $locations->toOptions('id', 'name', 'country_code');
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $response
            ->setData($locationData)
            ->success();

        echo $response;

        JFactory::getApplication()->close();
    }
}
