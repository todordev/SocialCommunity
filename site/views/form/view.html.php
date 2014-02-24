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

jimport('joomla.application.component.view');

class SocialCommunityViewForm extends JViewLegacy {
    
    protected $form       = null;
    protected $state      = null;
    protected $item       = null;
    
    protected $option     = null;
    
    protected $layoutsBasePath;
    
    public function __construct($config) {
    
        parent::__construct($config);
        
        $this->option = JFactory::getApplication()->input->get("option");
        $this->layoutsBasePath = JPath::clean(JPATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "layouts");
    
    }
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $this->layout = $this->getLayout();
        
        switch($this->layout) {
        
            case "contact":
                $this->prepareContact();
                break;
        
            case "social":
                $this->prepareSocialProfiles();
                break;
        
            default: // Basic data for the profile.
                $this->prepareBasic();
                break;
        }
        
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return;
        }
        
        // Prepare layout data.
        $this->layoutData = new JData();
        $this->layoutData->layout = $this->layout;
        
        // HTML Helpers
        JHtml::addIncludePath(ITPRISM_PATH_LIBRARY.'/ui/helpers');
        
        $this->prepareDocument();
        
        parent::display($tpl);
    }
    
    protected function prepareBasic() {
        
        $model              = JModelLegacy::getInstance("Basic", "SocialCommunityModel", $config = array('ignore_request' => false));
        
        $this->state        = $model->getState();
        $this->form         = $model->getForm();
        $this->params       = $this->state->get("params");
        
        $this->imagesFolder = $this->params->get("images_directory", "images/profiles");
        $this->item         = $model->getItem();
        
    }
    
    protected function prepareContact() {
    
        $model              = JModelLegacy::getInstance("Contact", "SocialCommunityModel", $config = array('ignore_request' => false));
        
        $this->state        = $model->getState();
        $this->form         = $model->getForm();
        $this->params       = $this->state->get("params");
        
        $this->item         = $model->getItem();
    
    }
    
    protected function prepareSocialProfiles() {
    
        $model              = JModelLegacy::getInstance("Social", "SocialCommunityModel", $config = array('ignore_request' => false));
    
        $this->state        = $model->getState();
        $this->form         = $model->getForm();
        $this->params       = $this->state->get("params");
    
        $this->items        = $model->getItems();
    
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        // Meta Description
        if(empty($category->metadesc)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($category->metadesc);
        }
        
        // Meta keywords
        if(empty($category->metakey)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $category->metakey);
        }
        
        // Add category name into breadcrumbs 
        $pathway    = $app->getPathway();
        $pathway->addItem(JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE"));
        
        // Head styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Script
        JHtml::_("bootstrap.tooltip");
        
        JHtml::_("itprism.ui.bootstrap_maxlength");
        JHtml::_("itprism.ui.bootstrap_fileuploadstyle");
        
        switch($this->layout) {
        
            case "contact":
                JHtml::_('itprism.ui.bootstrap_typeahead');
                JHtml::_('formbehavior.chosen', '#jform_country_id');
                
                $this->document->addScript('media/'.$this->option.'/js/site/form_contact.js');
                break;
        
            case "social":
                $this->prepareSocialProfiles();
                break;
        
            default: // Load scripts used on layout "Basic".
                $this->document->addScript('media/'.$this->option.'/js/site/form_basic.js');
                break;
                
        }
        
    }

    private function prepearePageHeading() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menus    = $app->getMenu();
		$menu     = $menus->getActive();
		
		// Prepare page heading
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE"));
        }
		
    }
    
    private function prepearePageTitle() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menus    = $app->getMenu();
		$menu     = $menus->getActive();
        
		// Prepare page title
        $title    = JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE");
        
        // Add title before or after Site Name
        if(!$title){
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
        $this->document->setTitle($title);
		
    }
    
}