<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class SocialCommunityController extends JControllerLegacy {
    
    protected $cacheableViews = array("profile");
    protected $option;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Method to display a view.
     *
     * @param   boolean         If true, the view output will be cached
     * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false) {

        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'INT',
            'limitstart'        => 'INT',
            'filter_order'      => 'CMD',
            'filter_order_dir'  => 'CMD',
            'catid'             => 'INT',
        );
        
        // Load component styles
        $doc = JFactory::getDocument();
        $doc->addStyleSheet("media/".$this->option.'/css/site/style.css');
        
        // Set the default view name and format from the Request.
        // Note we are using catid to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $viewName  = $this->input->getCmd('view', 'profile');
        $this->input->set('view', $viewName);

        // Cache some views.
        if(in_array($viewName, $this->cacheableViews)) {
            $cachable   = true;
        }
        
        return parent::display($cachable, $safeurlparams);
        
    }
	
} 