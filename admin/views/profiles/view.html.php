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
jimport('joomla.application.categories');

class SocialCommunityViewProfiles extends JView {
    
    protected $items;
    protected $pagination;
    protected $state;
    
    protected $option;
    
    protected $imagesFolder;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
        // Prepare filters
        $this->listOrder  = $this->escape($this->state->get('list.ordering'));
        $this->listDirn   = $this->escape($this->state->get('list.direction'));
        
        // Load the component parameters.
        $params              = JComponentHelper::getParams($this->option);
        $this->imagesFolder  = $params->get("images_directory", "images/profiles");
        
        // Include HTML helper
        JHtml::addIncludePath(JPATH_COMPONENT_SITE.'/helpers/html');
        
        // Add submenu
        SocialCommunityHelper::addSubmenu($this->getName());
        
        // Prepare actions
        $this->addToolbar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_SOCIALCOMMUNITY_PROFILES'), 'itp-profiles');
        JToolBarHelper::editList('profile.edit');
        JToolBarHelper::divider();
		JToolBarHelper::custom('profiles.create', "itp-profile-create", "", JText::_("COM_SOCIALCOMMUNITY_CREATE_PROFILE"), false);
        JToolBarHelper::divider();
        JToolBarHelper::deleteList(JText::_("COM_SOCIALCOMMUNITY_DELETE_ITEMS_QUESTION"), "profiles.delete");
        JToolBarHelper::divider();
        JToolBarHelper::custom('profiles.backToDashboard', "itp-dashboard-back", "", JText::_("COM_SOCIALCOMMUNITY_BACK_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_SOCIALCOMMUNITY_PROFILES_META_TITLE'));

		// Styles
		$this->document->addStyleSheet('../media/'.$this->option.'/css/admin/style.css');
		
		JHtml::_('behavior.tooltip');
	}
    
}