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

class SocialCommunityViewForm extends JViewLegacy
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

    protected $form;
    protected $item;
    protected $items;

    protected $option;

    protected $layout;
    protected $layoutData;
    protected $mediaFolder;
    protected $userId;
    protected $fileForCropping;
    protected $displayRemoveButton;
    protected $activeMenu;

    protected $pageclass_sfx;

    /**
     * @var $app JApplicationSite
     */
    protected $app;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise a Error object.
     *
     * @see     JViewLegacy::loadTemplate()
     * @since   12.2
     */
    public function display($tpl = null)
    {
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->get('option');
        $this->layout = $this->getLayout();

        $this->userId = JFactory::getUser()->get('id');
        if (!$this->userId) {
            $this->app->enqueueMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), 'notice');
            $this->app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return;
        }

        switch ($this->layout) {

            case 'avatar':
                $this->prepareAvatar();
                break;

            case 'contact':
                $this->prepareContact();
                break;

            case 'social':
                $this->prepareSocialProfiles();
                break;

            default: // Basic data for the profile.
                $this->prepareBasic();
                break;
        }

        // Prepare layout data.
        $this->layoutData         = new stdClass;
        $this->layoutData->layout = $this->layout;

        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareBasic()
    {
        $model = JModelLegacy::getInstance('Basic', 'SocialCommunityModel', $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get('params');

        $this->item   = $model->getItem();
    }

    protected function prepareAvatar()
    {
        $model = JModelLegacy::getInstance('Avatar', 'SocialCommunityModel', $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->params = $this->state->get('params');

        $filesystemHelper = new Prism\Filesystem\Helper($this->params);
        $this->mediaFolder = $filesystemHelper->getMediaFolderUri($this->userId);

        $this->item = $model->getItem($this->userId);

        $file = basename($this->app->getUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT));

        $this->fileForCropping = (!$file) ? null : JUri::base() . $filesystemHelper->getTemporaryMediaFolderUri() . '/' . $file;

        $this->displayRemoveButton = 'none';

        $js = '
        var socialCommunityOptions = {
            aspectRatio: '. $this->params->get('image_aspect_ratio', '') . '
        }';

        $this->document->addScriptDeclaration($js);
    }

    protected function prepareContact()
    {
        $model = JModelLegacy::getInstance('Contact', 'SocialCommunityModel', $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get('params');

        $this->item   = $model->getItem();
    }

    protected function prepareSocialProfiles()
    {
        $model = JModelLegacy::getInstance('Social', 'SocialCommunityModel', $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get('params');

        $this->items = $model->getItems();
    }

    /**
     * Prepares the document.
     */
    protected function prepareDocument()
    {
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if (!$this->params->get('metadesc')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->params->get('metadesc'));
        }

        // Meta keywords
        if (!$this->params->get('metakey')) {
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetaData('keywords', $this->params->get('metakey'));
        }

        // Create and add title into breadcrumbs.

        $this->activeMenu    = $this->app->getMenu()->getActive();
        if (array_key_exists('view', $this->activeMenu->query) and strcmp('form', $this->activeMenu->query['view']) !== 0) {
            $pathway = $this->app->getPathway();
            $pathway->addItem(JText::_('COM_SOCIALCOMMUNITY_EDIT_PROFILE'));
        }

        $version = new Socialcommunity\Version();

        JHtml::_('jquery.framework');

        switch ($this->layout) {

            case 'contact':
                JHtml::_('Prism.ui.bootstrap3Typeahead');

                if ($this->params->get('include_chosen', 0)) {
                    JHtml::_('formbehavior.chosen', '#jform_country_id');
                }

                $this->document->addScript('media/' . $this->option . '/js/site/form_contact.js');
                break;

            case 'avatar':
                JHtml::_('Prism.ui.cropper');
                JHtml::_('Prism.ui.fileupload');
                JHtml::_('Prism.ui.pnotify');
                JHtml::_('Prism.ui.joomlaHelper');
                JHtml::_('Prism.ui.serializeJson');
                JHtml::_('Prism.ui.isLoading');

                $this->document->addScript('media/' . $this->option . '/js/site/form_avatar.js?v='.$version->getShortVersion());

                // Load language string in JavaScript.
                JText::script('COM_SOCIALCOMMUNITY_QUESTION_REMOVE_IMAGE');
                JText::script('COM_SOCIALCOMMUNITY_CROPPING___');

                // Provide image size.
                $js = '
                    var scImageWidth  = ' . $this->params->get('image_width', 200) . ';
                    var scImageHeight = ' . $this->params->get('image_height', 200) . ';
                    var scUrl         = "' . JUri::base() . '"
                ';

                $this->document->addScriptDeclaration($js);
                break;

            default: // Load scripts used on layout 'Basic'.

                if ($this->params->get('include_chosen', 0)) {
                    JHtml::_('formbehavior.chosen', '#jform_gender');
                }

                JHtml::_('Prism.ui.bootstrapMaxLength');

                $this->document->addScript('media/' . $this->option . '/js/site/form_basic.js');

                break;
        }
    }

    private function preparePageHeading()
    {
        // Prepare page heading
        if ($this->activeMenu) {
            $this->params->def('page_heading', $this->params->get('page_title', $this->activeMenu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_SOCIALCOMMUNITY_EDIT_PROFILE'));
        }
    }

    private function preparePageTitle()
    {
        // Prepare page title
        $title = JText::_('COM_SOCIALCOMMUNITY_EDIT_PROFILE');

        // Add title before or after Site Name
        if (!$title) {
            $title = $this->app->get('sitename');
        } elseif ($this->app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ($this->app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
