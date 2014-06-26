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

jimport('itprism.controller.form.backend');

/**
 * SocialCommunity import controller.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityControllerImport extends ITPrismControllerFormBackend
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Import', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function locations()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            "view" => "locations",
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED"));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        $fileData = JArrayHelper::getValue($data, "data");
        if (empty($fileData) OR empty($fileData["name"])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);

            return;
        }

        try {

            jimport('joomla.filesystem.archive');
            jimport('itprism.file');
            jimport('itprism.file.uploader.local');
            jimport('itprism.file.validator.size');

            $destination = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . JFile::makeSafe($fileData['name']);

            $file = new ITPrismFile();

            // Prepare size validator.
            $KB       = 1024 * 1024;
            $fileSize = (int)$app->input->server->get('CONTENT_LENGTH');

            /** @var  $mediaParams Joomla\Registry\Registry */
            $mediaParams   = JComponentHelper::getParams("com_media");
            $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

            $sizeValidator = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);

            $file->addValidator($sizeValidator);

            // Validate the file
            $file->validate();

            // Prepare uploader object.
            $uploader = new ITPrismFileUploaderLocal($fileData);
            $uploader->setDestination($destination);

            // Upload the file
            $file->setUploader($uploader);
            $file->upload();

            $fileName = basename($destination);

            // Extract file if it is archive
            $ext = JString::strtolower(JFile::getExt($fileName));
            if (strcmp($ext, "zip") == 0) {

                $destFolder = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . "locations";
                if (is_dir($destFolder)) {
                    JFolder::delete($destFolder);
                }

                $filePath = $model->extractFile($destination, $destFolder);

            } else {
                $filePath = $destination;
            }

            $resetId   = JArrayHelper::getValue($data, "reset_id", false, "bool");
            $removeOld = JArrayHelper::getValue($data, "remove_old", false, "bool");

            $minPopulation = JArrayHelper::getValue($data, "minimum_population", 0, "int");

            if (!empty($removeOld)) {
                $model->removeAll("locations");
            }
            $model->importLocations($filePath, $resetId, $minPopulation);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_LOCATIONS_IMPORTED'), $redirectOptions);

    }

    public function countries()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            "view" => "countries",
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport * */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED"));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        $fileData = JArrayHelper::getValue($data, "data");
        if (empty($fileData) or empty($fileData["name"])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);

            return;
        }

        try {

            jimport('joomla.filesystem.archive');
            jimport('itprism.file');
            jimport('itprism.file.uploader.local');
            jimport('itprism.file.validator.size');

            $destination = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . JFile::makeSafe($fileData['name']);

            $file = new ITPrismFile();

            // Prepare size validator.
            $KB       = 1024 * 1024;
            $fileSize = (int)$app->input->server->get('CONTENT_LENGTH');

            /** @var  $mediaParams Joomla\Registry\Registry */
            $mediaParams   = JComponentHelper::getParams("com_media");
            $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

            $sizeValidator = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);

            $file->addValidator($sizeValidator);

            // Validate the file
            $file->validate();

            // Prepare uploader object.
            $uploader = new ITPrismFileUploaderLocal($fileData);
            $uploader->setDestination($destination);

            // Upload the file
            $file->setUploader($uploader);
            $file->upload();

            $fileName = basename($destination);

            // Extract file if it is archive
            $ext = JString::strtolower(JFile::getExt($fileName));
            if (strcmp($ext, "zip") == 0) {

                $destFolder = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . "countries";
                if (is_dir($destFolder)) {
                    JFolder::delete($destFolder);
                }

                $filePath = $model->extractFile($destination, $destFolder);

            } else {
                $filePath = $destination;
            }

            $resetId   = JArrayHelper::getValue($data, "reset_id", false, "bool");
            $removeOld = JArrayHelper::getValue($data, "remove_old", false, "bool");
            if (!empty($removeOld)) {
                $model->removeAll("countries");
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

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            "view" => "locations",
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_("COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED"));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        $fileData = JArrayHelper::getValue($data, "data");
        if (empty($fileData) or empty($fileData["name"])) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);

            return;
        }

        try {

            jimport('joomla.filesystem.archive');
            jimport('itprism.file');
            jimport('itprism.file.uploader.local');
            jimport('itprism.file.validator.size');

            $destination = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . JFile::makeSafe($fileData['name']);

            $file = new ITPrismFile();

            // Prepare size validator.
            $KB       = 1024 * 1024;
            $fileSize = (int)$app->input->server->get('CONTENT_LENGTH');

            /** @var  $mediaParams Joomla\Registry\Registry */
            $mediaParams   = JComponentHelper::getParams("com_media");
            $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

            $sizeValidator = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);

            $file->addValidator($sizeValidator);

            // Validate the file
            $file->validate();

            // Prepare uploader object.
            $uploader = new ITPrismFileUploaderLocal($fileData);
            $uploader->setDestination($destination);

            // Upload the file
            $file->setUploader($uploader);
            $file->upload();

            $fileName = basename($destination);

            // Extract file if it is archive
            $ext = JString::strtolower(JFile::getExt($fileName));
            if (strcmp($ext, "zip") == 0) {

                $destFolder = JPath::clean($app->get("tmp_path")) . DIRECTORY_SEPARATOR . "states";
                if (is_dir($destFolder)) {
                    JFolder::delete($destFolder);
                }

                $filePath = $model->extractFile($destination, $destFolder);

            } else {
                $filePath = $destination;
            }

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

        $view = $app->getUserState("import.context", "currencies");

        // Redirect to locations if the view is "states".
        if (strcmp("states", $view) == 0) {
            $view = "locations";
        }

        $link = $this->defaultLink . "&view=" . $view;
        $this->setRedirect(JRoute::_($link, false));
    }
}
