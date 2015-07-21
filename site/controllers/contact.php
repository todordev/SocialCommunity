<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Form controller class.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerContact extends Prism\Controller\Form\Frontend
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Check for registered user
        $userId = JFactory::getUser()->id;
        if (!$userId) {
            $redirectOptions = array(
                "force_direction" => "index.php?option=com_users&view=login"
            );

            $this->displayNotice(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), $redirectOptions);
            return;
        }

        $data            = $app->input->post->get('jform', array(), 'array');
        $redirectOptions = array(
            "view"   => "form",
            "layout" => "contact",
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelContact */

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

            $model->save($validData);

        } catch (Exception $e) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectOptions);

    }
}
