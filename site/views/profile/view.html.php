<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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

    protected $visitor;
    protected $visitorId;
    protected $isOwner;
    protected $targetId;
    protected $socialProfiles;
    protected $mediaFolder;
    protected $displayContactInformation;
    protected $formRegistration;
    protected $formLogin;
    protected $screen;

    protected $documentTitle;
    protected $breadcrumbTitle;
    protected $option;
    protected $wallPosts;
    protected $timezone;

    protected $pageclass_sfx;

    /**
     * @var JApplicationSite
     */
    protected $app;

    public function display($tpl = null)
    {
        $this->app       = JFactory::getApplication();
        $this->option    = $this->app->input->get('option');

        /** @var $model SocialCommunityModelProfile */
        $model           = $this->getModel();

        $this->state     = $model->getState();
        $this->item      = $model->getItem();

        $this->params    = $this->state->get('params');

        $this->visitorId = $this->state->get($this->option . '.visitor.id');
        $this->isOwner   = $this->state->get($this->option . '.visitor.is_owner');
        $this->targetId  = $this->state->get($this->option . '.target.user_id');

        // If I am not logged in, and I try to load profile page without user ID as a parameter,
        // I must load the layout of registration and login form.
        if (!$this->visitorId and !$this->item) {
            $this->setLayout('registration');
        }

        // If there is target ID but there is no valid item,
        // go on the view "No Profile". The profile does not exists.
        if ($this->targetId and !$this->item) {
            $this->setLayout('noprofile');
        }

        switch ($this->getLayout()) {

            case 'registration':
                $this->prepareRegistration();
                break;

            case 'noprofile':
                $this->prepareNoProfile();
                break;

            default: // default layout
                $filesystemHelper  = new Prism\Filesystem\Helper($this->params);
                $this->mediaFolder = $filesystemHelper->getMediaFolderUri($this->item->user_id);

                // Display details layout if
                // - visitor is not registered and there is target ID.
                // - visitor is registered but he is not the owner of the profile.
                if ((!$this->visitorId and $this->targetId) or ($this->visitorId and !$this->isOwner)) {
                    $this->setLayout('details'); // Details page.
                } else {
                    $this->setLayout('default'); // Profile owner's wall.
                }

                // If the wall has been disabled, display profile details.
                if (!$this->params->get('profile_wall', Prism\Constants::ENABLED)) {
                    $this->setLayout('details'); // Details page.
                }

                $pluginContext     = 'com_socialcommunity.profile';

                switch ($this->getLayout()) {

                    case 'default':

                        $pluginContext .= '.wall';

                        // If I am logged in and I try to load profile page,
                        // but I do not provide valid profile ID,
                        // I have to display error message.
                        if (!$this->item and $this->visitor->id > 0) {
                            $menu     = $this->app->getMenu();
                            $menuItem = $menu->getDefault();

                            $this->app->enqueueMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_PROFILE'), 'notice');
                            $this->app->redirect(JRoute::_('index.php?Itemid=' . $menuItem->id, false));
                            return;
                        }

                        $this->prepareWall();

                        break;

                    default: // details

                        $pluginContext .= '.details';
                        $this->prepareDetails();

                        break;
                }

                // Import content plugins
                JPluginHelper::importPlugin('content');

                // Events
                $dispatcher        = JEventDispatcher::getInstance();
                $this->item->event = new stdClass();
                $offset            = 0;

                $results = $dispatcher->trigger('onContentBeforeDisplay', array($pluginContext, &$this->item, &$this->params, $offset));
                $this->item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplayProfile', array($pluginContext, &$this->item, &$this->params));
                $this->item->event->beforeDisplayProfileContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplayProfile', array($pluginContext, &$this->item, &$this->params));
                $this->item->event->afterDisplayProfileContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array($pluginContext, &$this->item, &$this->params, $offset));
                $this->item->event->afterDisplayContent = trim(implode("\n", $results));

                break;
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareRegistration()
    {
        // Load the form.
        JForm::addFormPath(SOCIALCOMMUNITY_PATH_COMPONENT_SITE . '/models/forms');
        JForm::addFieldPath(SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . '/models/fields');

        $this->formRegistration = JForm::getInstance('com_socialcommunity.registration', 'registration', array('control' => 'jform', 'load_data' => false));
        $this->formLogin = JForm::getInstance('com_socialcommunity.login', 'login', array('load_data' => false));

        JHtml::_('behavior.formvalidator');

        $this->documentTitle = JText::sprintf('COM_SOCIALCOMMUNITY_REGISTRATION_S', $this->app->get('site'));
        $this->breadcrumbTitle = JText::_('COM_SOCIALCOMMUNITY_REGISTRATION');
    }

    /**
     * Prepare the wall of on user profile.
     */
    protected function prepareWall()
    {
        $this->documentTitle = JText::sprintf('COM_SOCIALCOMMUNITY_OWNER_S', $this->escape($this->item->name));
        $this->breadcrumbTitle = $this->escape($this->item->name);

        JHtml::_('jquery.framework');
        JHtml::_('Prism.ui.pnotify');
        JHtml::_('Prism.ui.joomlaHelper');
        JHtml::_('Prism.ui.bootstrapMaxLength');
        $this->document->addScript('media/com_socialcommunity/js/site/wall.js');

        JText::script('COM_SOCIALCOMMUNITY_WARNING');
        JText::script('COM_SOCIALCOMMUNITY_LENGTH_POST_D');

        $this->wallPosts = new Socialcommunity\Wall\User\Posts(JFactory::getDbo());
        $this->wallPosts->load(array('user_id' => $this->item->user_id));

        $user = JFactory::getUser();
        $this->timezone = $user->getParam('timezone');
    }

    /**
     * Prepare details page,
     * if there is target ID and the user is not signed on the site.
     */
    protected function prepareDetails()
    {
        $this->documentTitle = JText::sprintf('COM_SOCIALCOMMUNITY_PROFILE_PAGE_S', $this->escape($this->item->name));
        $this->breadcrumbTitle = $this->escape($this->item->name);
    }

    protected function prepareNoProfile()
    {
        $this->documentTitle = JText::_('COM_SOCIALCOMMUNITY_NO_PROFILE_PAGE');
        $this->breadcrumbTitle = JText::_('COM_SOCIALCOMMUNITY_NO_PAGE');
    }

    /**
     * Prepare the document
     */
    protected function prepareDocument()
    {
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetaData('robots', $this->params->get('robots'));
        }

        // Breadcrumb
        $pathway = $this->app->getPathway();
        $pathway->addItem($this->breadcrumbTitle);
    }

    private function preparePageHeading()
    {
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $this->app->getMenu();
        $menu  = $menus->getActive();

        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', $this->item->name);
        }
    }

    private function preparePageTitle()
    {
        // Because the application sets a default page title,
        // we need to get it from the menu item itself.
//        $menus = $this->app->getMenu();
//        $menu  = $menus->getActive();

        // Prepare page title
        $title = $this->documentTitle;

        // Add title before or after Site Name
        if (!$title) {
            $title = $this->app->get('sitename');
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
