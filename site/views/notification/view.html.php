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

class SocialCommunityViewNotification extends JViewLegacy
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

    protected $option;

    protected $version;

    protected $pageclass_sfx;

    /**
     * @var $app JApplicationSite
     */
    protected $app;

    public function display($tpl = null)
    {
        $this->app = JFactory::getApplication();
        
        $this->option = $this->app->input->get('option');

        $itemId = $this->app->input->getUint('id');
        $userId = JFactory::getUser()->get('id');

        $model = $this->getModel();

        // Initialise variables
        $this->item   = $model->getItem($itemId, $userId);
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        $notification = new Socialcommunity\Notification\Notification(JFactory::getDbo());
        $notification->load(array('id' => $itemId, 'user_id' => $userId));

        if ($notification->getId() and !$notification->isRead()) {
            $notification->updateStatus(Prism\Constants::READ);
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepare document
     */
    protected function prepareDocument()
    {
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetaData('robots', $this->params->get('robots'));
        }
    }

    private function preparePageHeading()
    {
        // Prepare page heading
        $this->params->def('page_heading', JText::_('COM_SOCIALCOMMUNITY_NOTIFICATION_DEFAULT_PAGE_TITLE'));

    }

    private function preparePageTitle()
    {
        // Prepare page title
        $title = JText::_('COM_SOCIALCOMMUNITY_NOTIFICATION_DEFAULT_PAGE_TITLE');

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
