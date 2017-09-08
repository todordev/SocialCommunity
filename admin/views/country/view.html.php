<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SocialcommunityViewCountry extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $state;
    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Prepare actions, behaviors, scripts and document
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ((int)$this->item->id === 0);

        $this->documentTitle = $isNew ? JText::_('COM_SOCIALCOMMUNITY_ADD_COUNTRY') : JText::_('COM_SOCIALCOMMUNITY_EDIT_COUNTRY');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('country.apply');
        JToolbarHelper::save2new('country.save2new');
        JToolbarHelper::save('country.save');

        if (!$isNew) {
            JToolbarHelper::cancel('country.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('country.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Scripts
        JHtml::_('behavior.formvalidation');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}
