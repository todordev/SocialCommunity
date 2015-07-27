<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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

    protected $form = null;
    protected $item = null;
    protected $items = null;

    protected $option = null;

    protected $layoutsBasePath;
    protected $layout;
    protected $layoutData;
    protected $imagesFolder;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->option          = JFactory::getApplication()->input->get("option");
        $this->layoutsBasePath = JPath::clean(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "layouts");
    }

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
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $this->layout = $this->getLayout();

        switch ($this->layout) {

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
        if (!$userId) {
            $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));

            return;
        }

        // Prepare layout data.
        $this->layoutData         = new JData();
        $this->layoutData->layout = $this->layout;

        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareBasic()
    {
        $model = JModelLegacy::getInstance("Basic", "SocialCommunityModel", $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get("params");

        $this->imagesFolder = $this->params->get("images_directory", "/images/profiles");
        $this->item         = $model->getItem();
    }

    protected function prepareContact()
    {
        $model = JModelLegacy::getInstance("Contact", "SocialCommunityModel", $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get("params");

        $this->item = $model->getItem();
    }

    protected function prepareSocialProfiles()
    {
        $model = JModelLegacy::getInstance("Social", "SocialCommunityModel", $config = array('ignore_request' => false));

        $this->state  = $model->getState();
        $this->form   = $model->getForm();
        $this->params = $this->state->get("params");

        $this->items = $model->getItems();
    }

    /**
     * Prepares the document.
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if (!$this->params->get("metadesc")) {
            $this->document->setDescription($this->params->get("menu-meta_description"));
        } else {
            $this->document->setDescription($this->params->get("metadesc"));
        }

        // Meta keywords
        if (!$this->params->get("metakey")) {
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $this->params->get("metakey"));
        }

        // Add category name into breadcrumbs
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE"));

        // Script
        JHtml::_('jquery.framework');

        switch ($this->layout) {

            case "contact":
                JHtml::_('prism.ui.bootstrap3Typeahead');

                if ($this->params->get("include_chosen", 0)) {
                    JHtml::_('formbehavior.chosen', '#jform_country_id');
                }

                $this->document->addScript('media/' . $this->option . '/js/site/form_contact.js');
                break;

            case "social":
                $this->prepareSocialProfiles();
                break;

            default: // Load scripts used on layout "Basic".

                if ($this->params->get("include_chosen", 0)) {
                    JHtml::_('formbehavior.chosen', '#jform_gender');
                }

                JHtml::_("prism.ui.bootstrapMaxLength");
                JHtml::_("prism.ui.bootstrap3FileInput");

                $this->document->addScript('media/' . $this->option . '/js/site/form_basic.js');

                JText::script('COM_SOCIALCOMMUNITY_SELECT_FILE');
                JText::script('COM_SOCIALCOMMUNITY_QUESTION_REMOVE_IMAGE');

                break;
        }

    }

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $app->getMenu();
        $menu  = $menus->getActive();

        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE"));
        }

    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
//        $menus = $app->getMenu();
//        $menu  = $menus->getActive();

        // Prepare page title
        $title = JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE");

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
