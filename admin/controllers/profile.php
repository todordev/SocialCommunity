<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

jimport('itprism.controller.form.backend');

/**
 * SocialCommunity Profile controller class.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 * @since		1.6
 */
class SocialCommunityControllerProfile extends ITPrismControllerFormBackend {

    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Profile', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Save an item
     */
    public function save() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Get form data 
        $data = $app->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "id");
        
        $redirectOptions = array(
            "task" => $this->getTask(), 
            "id"   => $itemId
        );
        
        $model = $this->getModel();
        /** @var $model SocialCommunityModelProfile **/
        
        $form = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if (! $form) {
            throw new Exception($model->getError(), 500);
        }
        
        // Validate form data
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
        
        try {
            
            // Get image
            $image   = $app->input->files->get('jform', array(), 'array');
            $image   = JArrayHelper::getValue($image, "photo");
            
            // Upload image
            if(!empty($image['name'])) {
            
                $imageNames    = $model->uploadImage($image);
                if(!empty($imageNames["image"])) {
                    $validData = array_merge($validData, $imageNames);
                }
            
            }
            
            $itemId = $model->save($validData);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectOptions);
    }
}