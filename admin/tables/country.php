<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class SocialCommunityTableCountry extends JTable
{
    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__itpsc_countries', 'id', $db);
    }
}
