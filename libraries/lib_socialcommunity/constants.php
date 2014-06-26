<?php
/**
 * @package      SocialCommunity
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * SocialCommunity constants
 *
 * @package      SocialCommunity
 * @subpackage   Constants
 */
class SocialCommunityConstants
{
    // States
    const PUBLISHED   = 1;
    const UNPUBLISHED = 0;
    const TRASHED     = -2;

    // Mail modes - html and plain text.
    const MAIL_MODE_HTML  = true;
    const MAIL_MODE_PLAIN = false;

    // Logs
    const ENABLE_SYSTEM_LOG  = true;
    const DISABLE_SYSTEM_LOG = false;

    // Location states
    const INCLUDE_COUNTRY_CODE  = true;

    // Notification states
    const ARCHIVED     = 1;
    const NOT_ARCHIVED = 0;
}
