<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * Form controller class.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerBasic extends ITPrismControllerFormFrontend
{
    /**
     * Save an item
     */
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Check for registered user
        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $redirectOptions = array(
                "force_direction" => "index.php?option=com_users&view=login"
            );

            $this->displayNotice(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), $redirectOptions);
            return;
        }

        $data            = $app->input->post->get('jform', array(), 'array');
        $redirectOptions = array(
            "view" => "form",
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelBasic */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_("COM_SOCIALCOMMUNITY_ERROR_FORM_LOADING"));
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        try {

            // Get image
            $image = $app->input->files->get('jform', array(), 'array');
            $image = JArrayHelper::getValue($image, "photo");

            // Upload image
            if (!empty($image['name'])) {

                $imageNames = $model->uploadImage($image);
                if (!empty($imageNames["image"])) {
                    $validData = array_merge($validData, $imageNames);
                }

            }

            $model->save($validData);

        } catch (Exception $e) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectOptions);

    }

    /**
     * Delete image
     */
    public function removeImage()
    {
        // Check for request forgeries.
        JSession::checkToken("get") or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $redirectOptions = array(
                "force_direction" => "index.php?option=com_users&view=login"
            );

            $this->displayNotice(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), $redirectOptions);

            return;
        }

        $redirectOptions = array(
            "view" => "form",
        );

        try {

            $model = $this->getModel();
            $model->removeImage($userId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_IMAGE_DELETED'), $redirectOptions);
    }
}
