<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * SocialCommunity contact controller.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerContact extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    object    The model.
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
        $query = $this->input->get->get('query', "", 'string');

        jimport('itprism.response.json');
        $response = new ITPrismResponseJson();

        try {

            $locations = new SocialCommunityLocations(JFactory::getDbo());
            $locations->loadByQuery($query);

            $locationData = $locations->toOptions();

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
