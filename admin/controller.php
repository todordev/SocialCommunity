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

/**
 * Default Controller
 *
 * @package         SocialCommunity
 * @subpackage      Components
 */
class SocialCommunityController extends JControllerLegacy
{
    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @param   boolean $cachable  If true, the view output will be cached
     * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
     *
     * @since   12.2
     */
    public function display($cachable = false, $urlparams = array())
    {
        $option = $this->input->getCmd('option');

        $viewName = $this->input->getCmd('view', 'dashboard');
        $this->input->set('view', $viewName);

        $doc = JFactory::getDocument();
        $doc->addStyleSheet('../media/' . $option . '/css/backend.style.css');

        parent::display();

        return $this;
    }
}
