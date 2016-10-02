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

jimport('itprism.controller.form.backend');

/**
 * SocialCommunity import controller.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityControllerImport extends Prism\Controller\Form\Backend
{
    public function getModel($name = 'Import', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function locations()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            'view' => 'locations',
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        $fileData = Joomla\Utilities\ArrayHelper::getValue($data, 'data');
        if (!$fileData or empty($fileData['name'])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);
            return;
        }

        try {

            $filePath = $model->uploadFile($fileData, 'locations');

            $resetId   = Joomla\Utilities\ArrayHelper::getValue($data, 'reset_id', false, 'bool');
            $removeOld = Joomla\Utilities\ArrayHelper::getValue($data, 'remove_old', false, 'bool');

            $minPopulation = Joomla\Utilities\ArrayHelper::getValue($data, 'minimum_population', 0, 'int');

            if ($removeOld) {
                $model->removeAll('locations');
            }
            $model->importLocations($filePath, $resetId, $minPopulation);

        } catch (RuntimeException $e) {

            $this->displayError(JString::substr($e->getMessage(), 0, 255), $redirectOptions);
            return;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_LOCATIONS_IMPORTED'), $redirectOptions);
    }

    public function countries()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            'view' => 'countries',
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        $fileData = Joomla\Utilities\ArrayHelper::getValue($data, 'data');
        if (!$fileData or empty($fileData['name'])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);
            return;
        }

        try {

            $filePath = $model->uploadFile($fileData, 'countries');

            $resetId   = Joomla\Utilities\ArrayHelper::getValue($data, 'reset_id', false, 'bool');
            $removeOld = Joomla\Utilities\ArrayHelper::getValue($data, 'remove_old', false, 'bool');
            if ($removeOld) {
                $model->removeAll('countries');
            }
            $model->importCountries($filePath, $resetId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_COUNTRIES_IMPORTED'), $redirectOptions);
    }

    public function states()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            'view' => 'locations',
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        $fileData = Joomla\Utilities\ArrayHelper::getValue($data, 'data');
        if (!$fileData or empty($fileData['name'])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);
            return;
        }

        try {

            $filePath = $model->uploadFile($fileData, 'states');

            $model->importStates($filePath);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_STATES_IMPORTED'), $redirectOptions);
    }

    public function cancel($key = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $view = $app->getUserState('import.context', 'countries');

        // Redirect to locations if the view is 'states'.
        if (strcmp('states', $view) === 0) {
            $view = 'locations';
        }

        $link = $this->defaultLink . '&view=' . $view;
        $this->setRedirect(JRoute::_($link, false));
    }
}
