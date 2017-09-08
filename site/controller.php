<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class SocialcommunityController extends JControllerLegacy
{
    protected $cachableViews = array('profile');

    /**
     * Method to display a view.
     *
     * @param   boolean    $cachable     If true, the view output will be cached
     * @param   array      $urlparams     An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array())
    {
        $safeUrlParams = array(
            'id'               => 'INT',
            'limit'            => 'INT',
            'limitstart'       => 'INT',
            'filter_order'     => 'CMD',
            'filter_order_dir' => 'CMD',
            'catid'            => 'INT',
        );

        // Load component styles
        JHtml::stylesheet('com_socialcommunity/frontend.style.css', false, true, false);
        JHtml::_('Prism.ui.styles');

        // Set the default view name and format from the Request.
        // Note we are using catid to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $viewName = $this->input->getCmd('view', 'profile');
        $this->input->set('view', $viewName);

        // Cache some views.
        if (in_array($viewName, $this->cachableViews, true)) {
            $cachable = true;
        }

        return parent::display($cachable, $safeUrlParams);
    }
}
