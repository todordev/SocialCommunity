<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use \Socialcommunity\Notification\Gateway\JoomlaGateway;
use \Socialcommunity\Notification\Mapper;
use \Socialcommunity\Notification\Repository;
use \Socialcommunity\Notification\Notification;

/**
 * Socialcommunity notification controller.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerNotification extends JControllerLegacy
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
    public function getModel($name = 'Notification', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * This method removes a notification.
     */
    public function remove()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $itemId = $this->input->getUint('id');
        $userId = JFactory::getUser()->get('id');

        $response = new Prism\Response\Json();

        $validatorOwner = new Socialcommunity\Validator\Notification\Owner($itemId, $userId);
        $validatorOwner->setGateway(new \Socialcommunity\Validator\Notification\Gateway\Joomla\Owner(JFactory::getDbo()));
        if (!$validatorOwner->isValid()) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_NOTIFICATION'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $mapper       = new Mapper(new JoomlaGateway(JFactory::getDbo()));
            $repository   = new Repository($mapper);
            $notification = new Notification();
            $notification->setId($itemId);

            $repository->delete($notification);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $response
            ->setTitle(JText::_('COM_SOCIALCOMMUNITY_SUCCESS'))
            ->setContent(JText::_('COM_SOCIALCOMMUNITY_NOTIFICATION_REMOVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }
}
