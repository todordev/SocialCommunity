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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class SocialCommunityViewForm extends JView {
    
    protected $form       = null;
    protected $state      = null;
    protected $item       = null;
    
    protected $option     = null;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->form       = $this->get('Form');
        $this->params     = $this->state->get("params");
            
        $this->imagesFolder = $this->params->get("images_directory", "images/profiles")."/";
        $this->item         = $this->get("Item");
        
        if(!$this->item) {
            $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return;
        }
        
        $this->prepareDocument();
        
        parent::display($tpl);
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
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/bootstrap.min.css');
        $this->document->addStyleSheet('media/'.$this->option.'/css/bootstrap-fileupload.min.css');
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Script
        $this->document->addScript('media/'.$this->option.'/js/bootstrap.min.js');
        $this->document->addScript('media/'.$this->option.'/js/bootstrap-maxlength.min.js');
        $this->document->addScript('media/'.$this->option.'/js/bootstrap-fileupload.min.js');
        $this->document->addScript('media/'.$this->option.'/js/site/form.js');
        
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