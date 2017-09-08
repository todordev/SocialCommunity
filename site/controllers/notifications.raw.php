<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use \Socialcommunity\Notification\Service\Gateway\Joomla\Counter;

/**
 * Socialcommunity notifications controller.
 *
 * @package     Gamification Platform
 * @subpackage  Components
 */
class SocialcommunityControllerNotifications extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialcommunityModelNotifications    The model.
     * @since    1.5
     */
    public function getModel($name = 'Notifications', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Method to load data via AJAX
     */
    public function getNumber()
    {
        $response = new Prism\Response\Json();

        try {
            $options = array(
                'user_id' => JFactory::getUser()->get('id'),
                'status'  => Prism\Constants::NOT_READ
            );

            $counter = new Socialcommunity\Notification\Service\Counter(new Counter(JFactory::getDbo()));
            $notRead = $counter->getNotificationsNumber($options);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_CHALLENGES_ERROR_SYSTEM'));
        }

        $data = array('results' => $notRead);

        $response
            ->setData($data)
            ->success();

        echo $response;
        JFactory::getApplication()->close();
    }
}
