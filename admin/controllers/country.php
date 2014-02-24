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
 * SocialCommunity country controller class.
 *
 * @package		SocialCommunity
 * @subpackage	Components
 * @since		1.6
 */
class SocialCommunityControllerCountry extends ITPrismControllerFormBackend {
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $redirectData = array(
            "task"  => $this->getTask(),
            "id"    => $itemId
        );
        
        $model   = $this->getModel();
        /** @var $model SocialCommunityModelCountry **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form
        $validData = $model->validate($form, $data);
        
        // Check for errors
        if($validData === false){
            $this->displayNotice($form->getErrors(), $redirectData);
            return;
        }
            
        try {
            $itemId = $model->save($validData);
        } catch (Exception $e) {
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        
        }
        
        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_COUNTRY_SAVED'), $redirectData);
    
    }
    
}