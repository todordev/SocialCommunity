<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SocialcommunityModelNotification extends JModelItem
{
    protected $item = array();

    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }
}
