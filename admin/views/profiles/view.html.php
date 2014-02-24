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
jimport('joomla.application.categories');

class SocialCommunityViewProfiles extends JViewLegacy {
    
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
        
        // Load the component parameters.
        $params              = JComponentHelper::getParams($this->option);
        $this->imagesFolder  = $params->get("images_directory", "images/profiles");
        
        // Add submenu
        SocialCommunityHelper::addSubmenu($this->getName());
        
        // Prepare sorting data
        $this->prepareSorting();
        
        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting() {
    
        // Prepare filters
        $this->listOrder  = $this->escape($this->state->get('list.ordering'));
        $this->listDirn   = $this->escape($this->state->get('list.direction'));
        $this->saveOrder  = (strcmp($this->listOrder, 'a.ordering') != 0 ) ? false : true;
    
        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option='.$this->option.'&task='.$this->getName().'.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName().'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }
    
        $this->sortFields = array(
            'a.name'            => JText::_('COM_SOCIALCOMMUNITY_NAME'),
            'b.registerDate'    => JText::_('COM_SOCIALCOMMUNITY_REGISTERED'),
            'a.id'              => JText::_('JGRID_HEADING_ID')
        );
    
    }
    
    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
    
        JHtmlSidebar::setAction('index.php?option='.$this->option.'&view='.$this->getName());
    
        $states = array(
            JHtml::_("select.option", "0", JText::_("COM_SOCIALCOMMUNITY_DOES_NOT_EXISTS")),
            JHtml::_("select.option", "1", JText::_("COM_SOCIALCOMMUNITY_EXISTS"))
        );
        
        JHtmlSidebar::addFilter(
            JText::_('COM_SOCIALCOMMUNITY_SELECT_PROFILE_STATE'),
            'filter_profile',
            JHtml::_('select.options', $states, 'value', 'text', $this->state->get('filter.profile'), true)
        );
    
        $this->sidebar = JHtmlSidebar::render();
    
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_SOCIALCOMMUNITY_PROFILES_MANAGER'));
        JToolBarHelper::editList('profile.edit');
        JToolBarHelper::divider();
		JToolBarHelper::custom('profiles.create', "plus", "", JText::_("COM_SOCIALCOMMUNITY_CREATE_PROFILE"), false);
        JToolBarHelper::divider();
        JToolBarHelper::deleteList(JText::_("COM_SOCIALCOMMUNITY_DELETE_ITEMS_QUESTION"), "profiles.delete");
        JToolBarHelper::divider();
        JToolBarHelper::custom('profiles.backToDashboard', "dashboard", "", JText::_("COM_SOCIALCOMMUNITY_BACK_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_SOCIALCOMMUNITY_PROFILES_MANAGER'));

		// Scripts
		JHtml::_('behavior.multiselect');
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('bootstrap.tooltip');
		
		$this->document->addScript('../media/'.$this->option.'/js/admin/list.js');
	}
    
}