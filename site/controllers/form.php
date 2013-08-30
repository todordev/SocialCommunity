<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
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
class SocialCommunityControllerForm extends ITPrismControllerFormFrontend {
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Check for registered user
        $userId  = JFactory::getUser()->id;
        if(!$userId) {
            $redirectData = array(
                "force_direction" => "index.php?option=com_users&view=login"
            );
            
            $this->displayNotice(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), $redirectData);
            return;
        }
        
        $data    = $app->input->post->get('jform', array(), 'array');
        $redirectData = array (
            "view"    => "form",
        );
        
        // Get image
        $image   = $app->input->files->get('jform', array(), 'array');
        $image   = JArrayHelper::getValue($image, "photo");
        
        $model   = $this->getModel();
        /** @var $model SocialCommunityModelForm **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError());
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayNotice($form->getErrors(), $redirectData);
            return;
        }
            
        try {
            
            // Upload image
            if(!empty($image['name'])) {
                
                jimport('joomla.filesystem.folder');
                jimport('joomla.filesystem.file');
                jimport('joomla.filesystem.path');
                jimport('joomla.image.image');
                jimport('itprism.file.upload.image');
                
                $imageNames    = $model->uploadImage($image);
                if(!empty($imageNames["image"])) {
                    $validData = array_merge($validData, $imageNames);
                }
                
            }
            
            $model->save($validData);
            
        } catch (Exception $e){
            throw new Exception( JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'), 500);
        }
        
        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectData);
    
    }
    
	/**
     * Delete image
     */
    public function removeImage() {
        
        // Check for request forgeries.
        JSession::checkToken("get") or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Check for registered user
        $userId  = JFactory::getUser()->id;
        if(!$userId) {
            $redirectData = array(
                "force_direction"     => "index.php?option=com_users&view=login"
            );
            
            $this->displayNotice(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), $redirectData);
            return;
        }
        
        $redirectData = array (
            "view"     => "form",
        );
        
        try {
            
            $model = $this->getModel();
            $model->removeImage($userId);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_IMAGE_DELETED'), $redirectData);
        
    }
    
    
}