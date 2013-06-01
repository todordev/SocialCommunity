<?php
/**
 * @package		 ITPrism Library
 * @subpackage	 Social Community
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPrism Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

JLoader::register("SocialCommunityTableProfile", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_socialcommunity".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."profile.php");

class SocialCommunityProfile extends SocialCommunityTableProfile {

    protected static $instances = array();

    public function __construct($id = 0){
    
        // Set database driver
        $db = JFactory::getDbo();
        parent::__construct($db);
    
        if(!empty($id)) {
            $this->load($id);
        }
    }
    
    public static function getInstance($id = 0)  {
    
        if (empty(self::$instances[$id])){
            $item = new SocialCommunityProfile($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
    }
    
    
    
    
}
