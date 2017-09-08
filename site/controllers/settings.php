<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Socialcommunity profile controller
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerSettings extends Prism\Controller\Form\Frontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialcommunityModelSettings   The model.
     * @since    1.5
     */
    public function getModel($name = 'Settings', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        $data            = $this->input->post->get('jform', array(), 'array');
        $redirectOptions = array(
            'view' => 'settings'
        );

        $model = $this->getModel();
        /** @var $model SocialcommunityModelBasic */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new RuntimeException(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_LOADING'));
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        try {
            $validData['id'] = $userId;

            $model->save($validData);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_SETTINGS_SAVED'), $redirectOptions);
    }
}
