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
    protected $objectId;
    protected $socialProfiles;
    protected $imagesFolder;
    protected $displayContactInformation;
    protected $version;

    protected $documentTitle;
    protected $option;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        /** @var $model SocialCommunityModelProfileItem */
        $model = JModelLegacy::getInstance("ProfileItem", "SocialCommunityModel", array('ignore_request' => false));

        // Get the current user as a "Visitor"
        $this->visitor = JFactory::getUser();

        $this->state  = $model->getState();
        $this->item   = $model->getItem();

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        $this->imagesFolder = $this->params->get("images_directory", "/images/profiles");

        // If I am not logged in, and I try to load profile page without user ID as a parameter,
        // I must be redirected to login form
        if (empty($this->visitor->id) and !$this->item) {
            $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));

            return;
        }

        // If I am logged in and I try to load profile page,
        // but I have not provided a valid profile ID.
        // I have to display error message.
        if (!$this->item and !empty($this->visitor->id)) {

            $menu     = $app->getMenu();
            $menuItem = $menu->getDefault();

            $app->enqueueMessage(JText::_("COM_SOCIALCOMMUNITY_ERROR_INVALID_PROFILE"), "notice");
            $app->redirect(JRoute::_('index.php?Itemid=' . $menuItem->id, false));

            return;
        }

        $this->visitorId = $this->state->get($this->option . '.visitor.id');
        $this->isOwner   = $this->state->get($this->option . '.visitor.is_owner');
        $this->objectId  = $this->state->get($this->option . '.profile.user_id');

        // Get social profiles
        $this->socialProfiles = new SocialCommunity\SocialProfiles(JFactory::getDbo());
        $this->socialProfiles->load(array("id" => $this->objectId));

        $this->prepareBasicInformation();
        $this->prepareContactInformation();

        $this->version = new SocialCommunity\Version();

        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareBasicInformation()
    {
        $this->item->bio = JString::trim($this->item->bio);
    }

    protected function prepareContactInformation()
    {
        $this->item->address  = JString::trim($this->item->address);
        $this->item->phone    = JString::trim($this->item->phone);
        $this->item->country  = JString::trim($this->item->country);
        $this->item->location = JString::trim($this->item->location);

        $this->displayContactInformation = false;
        if (!empty($this->item->address) or !empty($this->item->phone) or !empty($this->item->country) or !empty($this->item->location)) {
            $this->displayContactInformation = true;
        }
    }

    /**
     * Prepare the document
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

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Breadcrumb
        $pathway = $app->getPathWay();
        $pathway->addItem($this->item->name);
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
            $this->params->def('page_heading', $this->item->name);
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
        $title = $this->item->name;

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
