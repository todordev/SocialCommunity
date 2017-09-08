<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \Socialcommunity\Notification\Command\Gateway\Joomla\UpdateStatus as UpdateStatusGateway;
use \Socialcommunity\Notification\Command\UpdateStatus as UpdateStatusCommand;
use \Prism\Database\Condition\Condition;
use \Prism\Database\Condition\Conditions;
use \Prism\Database\Request\Request;

// no direct access
defined('_JEXEC') or die;

class SocialcommunityViewNotification extends JViewLegacy
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
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->get('option');

        $itemId = $this->app->input->getUint('id');
        $userId = JFactory::getUser()->get('id');

        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        // Prepare conditions.
        $conditionId = new Condition(['column' => 'id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
        $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
        $conditions = new Conditions();
        $conditions
            ->addCondition($conditionId)
            ->addCondition($conditionUserId);

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        $mapper     = new \Socialcommunity\Notification\Mapper(new \Socialcommunity\Notification\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new \Socialcommunity\Notification\Repository($mapper);
        $notification = $repository->fetch($databaseRequest);

        if ($notification->getId() and !$notification->isRead()) {
            $command = new UpdateStatusCommand($itemId, Prism\Constants::READ);
            $command->setGateway(new UpdateStatusGateway(JFactory::getDbo()));
            $command->handle();
        }

        $this->item = $notification;

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

        $pathway = $this->app->getPathway();
        $pathway->addItem(JText::_('COM_SOCIALCOMMUNITY_NOTIFICATION'));
    }

    private function preparePageHeading()
    {
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
