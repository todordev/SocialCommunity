<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
class SocialCommunityTableLocation extends JTable {
    
    public function __construct( $db ) {
        parent::__construct( '#__itpsc_locations', 'id', $db );
    }
    
}