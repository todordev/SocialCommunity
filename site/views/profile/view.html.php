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

class SocialCommunityViewProfile extends JViewLegacy {
    
    protected $state;
    protected $item;
    protected $params;
    
    protected $documentTitle;
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Get the current user as a "Visitor"
        $this->visitor      = JFactory::getUser();
            
		$this->state	    = $this->get('State');
		$this->item		    = $this->get("Item");
		$this->params	    = $this->state->get("params");
		
		$this->imagesFolder = $this->params->get("images_directory", "images/profiles");

		// If I am not logged in, and I try to load profile page without user ID as a parameter, 
		// I must be redirected to login form
        if(empty($this->visitor->id) AND !$this->item) {
		    $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false)); 
            return;
		}
		
		// If I am logged in and I try to load profile page,
		// but I have not provided a valid profile ID.
		// I have to display error message.
		if( !$this->item AND !empty($this->visitor->id) ) {
		    
            $menu     = $app->getMenu();
            $menuItem = $menu->getDefault();

		    $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_INVALID_PROFILE"), "notice");
            $app->redirect(JRoute::_('index.php?Itemid='.$menuItem->id, false)); 
            return;
		}
		
		$this->visitorId = $this->state->get($this->option.'.visitor.id');
		$this->isOwner   = $this->state->get($this->option.'.visitor.is_owner');
		$this->objectId  = $this->state->get($this->option.'.profile.user_id');
		
		// Get social profiles
		jimport("socialcommunity.socialprofiles");
		$this->socialProfiles = new SocialCommunitySocialProfiles(JFactory::getDbo());
		$this->socialProfiles->load($this->objectId);
		
		$this->prepareBasicInformation();
		$this->prepareContactInformation();
		    
		$this->version   = new SocialCommunityVersion();

		$this->prepareDocument();
		
		parent::display($tpl);
    }
    
    protected function prepareBasicInformation() {
        
        $this->item->bio  = JString::trim($this->item->bio);
        
    }
    
    protected function prepareContactInformation() {
        
        $this->item->address  = JString::trim($this->item->address);
        $this->item->phone    = JString::trim($this->item->phone);
        $this->item->country  = JString::trim($this->item->country);
        $this->item->location = JString::trim($this->item->location);
        
        $this->displayContactInformation = false;
        if(!empty($this->item->address) OR !empty($this->item->phone) OR !empty($this->item->country) OR !empty($this->item->location)){
            $this->displayContactInformation = true;
        }
        
        $this->displayAddress = false;
        if(!empty($this->item->address) OR !empty($this->item->country) OR !empty($this->item->location)) {
            $this->displayAddress = true;
        }
        
    }
    
    /**
     * Prepare the document
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

		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		// Breadcrumb
		$pathway = $app->getPathWay();
		$pathway->addItem($this->item->name);
		
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
            $this->params->def('page_heading', $this->item->name);
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
        $title    = $this->item->name;
        
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