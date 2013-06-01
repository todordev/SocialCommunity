<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class SocialCommunityModelDetails extends JModelItem {
    
    protected $item = array();
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState() {
		
	    $app	 = JFactory::getApplication();
		/** @var $app JSite **/
	    
		$params	 = $app->getParams($this->option);
		$this->setState('params', $params);
		
		// Visitor
		$visitorId   = (int)JFactory::getUser()->get("id");
		$this->setState($this->option.'.visitor.id', $visitorId);
		
		$userId      = $app->input->get->getInt("id");
		if(!$userId) {
		    $userId = (int)JFactory::getUser()->get("id");
		}
		$this->setState($this->option.'.profile.user_id', $userId);
		
	    $value   = ($userId == $visitorId) ? true : false;
	    $this->setState($this->option.'.visitor.is_owner', $value);
	}

	/**
	 * Method to get an object.
	 *
	 * @param	integer	The id of the object to get.
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
	    if (!$id) {
	        $id = $this->getState($this->option.".profile.user_id");
		}
		
		$storedId = $this->getStoreId($id);
		
		if (!isset($this->item[$storedId])) {

		    $db     = JFactory::getDbo();
		    $query  = $db->getQuery(true);
		    $query
		        ->select("*")
		        ->from("#__itpsc_profiles")
		        ->where("id = " . (int)$id);

		    $db->setQuery($query, 0, 1);
		    $result = $db->loadAssoc();
            
			// Check published state.
			if (empty($result)){
				return null;
			}

			$this->item[$storedId] = $result;
			
		}

		return $this->item[$storedId];
	}
	
}