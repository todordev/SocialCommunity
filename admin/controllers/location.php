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

jimport('itprism.controller.form.backend');

/**
 * SocialCommunity location controller class.
 *
 * @package        SocialCommunity
 * @subpackage     Components
 * @since          1.6
 */
class SocialCommunityControllerLocation extends ITPrismControllerFormBackend
{
    /**
     * Save an item
     */
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "id");

        $redirectData = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );

        $model = $this->getModel();
        /** @var $model SocialCommunityModelLocation */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED"));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectData);

            return;
        }

        try {

            $itemId = $model->save($validData);

            $redirectData["id"] = $itemId;

        } catch (Exception $e) {

            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));

        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_LOCATION_SAVED'), $redirectData);
    }
}
