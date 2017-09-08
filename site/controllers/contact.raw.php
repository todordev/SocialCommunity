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

/**
 * Socialcommunity contact controller.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerContact extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialcommunityModelContact|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Contact', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
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
        $query       = $this->input->get->getCmd('query');
        $countryCode = $this->input->get->getCmd('country_code');

        // Prepare conditions.
        $conditionSearch = new Condition(['column' => 'name', 'value' => $query, 'operator'=> 'LIKE', 'table' => 'a']);
        $conditions      = new Conditions();
        $conditions->addSpecificCondition('search', $conditionSearch);

        if ($countryCode) {
            $conditionCountryCode = new Condition(['column' => 'country_code', 'value' => $countryCode, 'operator'=> '=', 'table' => 'a']);
            $conditions->addSpecificCondition('country_code', $conditionCountryCode);
        }

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        $response = new Prism\Response\Json();

        try {
            $mapper     = new Socialcommunity\Location\Mapper(new \Socialcommunity\Location\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new Socialcommunity\Location\Repository($mapper);
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
        JFactory::getApplication()->close();
    }
}
