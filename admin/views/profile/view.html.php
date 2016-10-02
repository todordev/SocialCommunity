<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SocialCommunityViewProfile extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $item;
    protected $form;

    protected $mediaFolder;

    protected $documentTitle;
    protected $option;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->params = $this->state->get('params');

        if (!$this->item->id) {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('COM_SOCIALCOMMUNITY_NO_PROFILE'), 'notice');
            $app->redirect(JRoute::_('index.php?option=com_socialcommunity&view=profiles', false));

            return;
        }

        $filesystemHelper  = new Prism\Filesystem\Helper($this->params);
        $this->mediaFolder = $filesystemHelper->getMediaFolderUri($this->item->user_id);

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

        $this->documentTitle = $isNew ? JText::_('COM_SOCIALCOMMUNITY_NEW_PROFILE') : JText::_('COM_SOCIALCOMMUNITY_EDIT_PROFILE');

        if (!$isNew) {
            JToolbarHelper::title($this->documentTitle, 'itp-profile-edit');
        } else {
            JToolbarHelper::title($this->documentTitle, 'itp-profile-add');
        }

        JToolbarHelper::apply('profile.apply');
        JToolbarHelper::save('profile.save');

        if (!$isNew) {
            JToolbarHelper::cancel('profile.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Add behaviors
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.formvalidation');

        JHtml::_('formbehavior.chosen', '#jform_country_id');

        JHtml::_('Prism.ui.bootstrap2Typeahead');

        // Add scripts
        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}
