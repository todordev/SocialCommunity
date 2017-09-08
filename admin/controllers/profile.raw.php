<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Socialcommunity profile controller.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerProfile extends JControllerLegacy
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
    public function getModel($name = 'Contact', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
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
        $app         = JFactory::getApplication();

        $query       = $app->input->get->get('query', '', 'string');
        $query       = explode(',', $query);
        $query       = $query[0];

        $countryCode = $app->input->get->getCmd('country_code');

        $response = new Prism\Response\Json();

        try {
            $conditionCountryId = new \Prism\Database\Condition\Condition(['column'=> 'country_code', 'value' => $countryCode, 'table'=>'a', 'operator' => '=']);
            $conditionSearch    = new \Prism\Database\Condition\Condition(['column'=> 'name', 'value' => $query, 'operator' => 'LIKE']);

            $conditions = new \Prism\Database\Condition\Conditions();
            $conditions
                ->addCondition($conditionCountryId)
                ->addSpecificCondition('search', $conditionSearch);

            $databaseRequest = new \Prism\Database\Request\Request();
            $databaseRequest->setConditions($conditions);

            $mapper = new \Socialcommunity\Location\Mapper(new \Socialcommunity\Location\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new \Socialcommunity\Location\Repository($mapper);

            $locations  = $repository->fetchCollection($databaseRequest);
            $locationData = $locations->toOptions('id', 'name', 'country_code');
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $response
            ->setData($locationData)
            ->success();

        echo $response;
        $app->close();
    }
}
