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

class SocialCommunityViewCountries extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

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
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.name'      => JText::_('COM_SOCIALCOMMUNITY_NAME'),
            'a.code'      => JText::_('COM_SOCIALCOMMUNITY_COUNTRY_CODE'),
            'a.code4'     => JText::_('COM_SOCIALCOMMUNITY_COUNTRY_CODE4'),
            'a.latitude'  => JText::_('COM_SOCIALCOMMUNITY_COUNTRY_LATITUDE'),
            'a.longitude' => JText::_('COM_SOCIALCOMMUNITY_COUNTRY_LONGITUDE'),
            'a.timezone'  => JText::_('COM_SOCIALCOMMUNITY_COUNTRY_TIMEZONE'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_SOCIALCOMMUNITY_COUNTRIES_MANAGER'));
        JToolbarHelper::addNew('country.add');
        JToolbarHelper::editList('country.edit');
        JToolbarHelper::divider();

        // Add custom buttons
        $bar = JToolbar::getInstance('toolbar');

        // Import
        $link = JRoute::_('index.php?option=com_socialcommunity&view=import&type=countries');
        $bar->appendButton('Link', 'upload', JText::_("COM_SOCIALCOMMUNITY_IMPORT"), $link);

        // Export
        $link = JRoute::_('index.php?option=com_socialcommunity&task=export.download&format=raw&type=countries');
        $bar->appendButton('Link', 'download', JText::_("COM_SOCIALCOMMUNITY_EXPORT"), $link);

        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_SOCIALCOMMUNITY_DELETE_ITEMS_QUESTION"), "countries.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('countries.backToDashboard', "dashboard", "", JText::_("COM_SOCIALCOMMUNITY_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_SOCIALCOMMUNITY_COUNTRIES_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('itprism.ui.joomla_list');
    }
}
