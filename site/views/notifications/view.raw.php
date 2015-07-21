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

class SocialCommunityViewNotifications extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $params;
    protected $pagination;

    protected $option;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        // Initialise variables
        $this->items  = $this->get('Items');
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }
}
