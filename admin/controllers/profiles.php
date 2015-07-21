<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

jimport('itprism.controller.admin');

/**
 * SocialCommunity Profiles Controller
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerProfiles extends Prism\Controller\Admin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Profile', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function create()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Get form data
        $pks   = $app->input->post->get('cid', array(), 'array');
        $model = $this->getModel("Profile", "SocialCommunityModel");
        /** @var $model SocialCommunityModelProfile */

        $redirectOptions = array(
            "view" => $this->view_list
        );

        JArrayHelper::toInteger($pks);

        // Check for validation errors.
        if (empty($pks)) {
            $this->displayWarning(JText::_("COM_SOCIALCOMMUNITY_INVALID_ITEM"), $redirectOptions);
            return;
        }

        try {

            $pks = $model->filterProfiles($pks);

            if (!$pks) {
                $this->displayWarning(JText::_("COM_SOCIALCOMMUNITY_INVALID_ITEM"), $redirectOptions);
                return;
            }

            $model->create($pks);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::plural('COM_SOCIALCOMMUNITY_N_PROFILES_CREATED', count($pks)), $redirectOptions);
    }

}
