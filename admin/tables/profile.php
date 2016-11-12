<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SocialcommunityTableProfile extends JTable
{
    /**
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__itpsc_profiles', 'id', $db);
    }
}
