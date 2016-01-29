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
 * SocialCommunity HTML Backend Helper
 *
 * @package        SocialCommunity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlSocialCommunityBackend
{
    public static function boolean($value, $title = '')
    {
        $title = addslashes(htmlspecialchars(JString::trim($title), ENT_COMPAT, 'UTF-8'));

        $class = 'check-circle-0';
        if (!$value) { // unpublished
            $class = 'times-circle-0';
        }

        if ($title !== '') {
            $title = ' title="' . $title . '"';
        }

        $html[] = '<a class="btn btn-micro" rel="tooltip" href="javascript:void(0);" ' . $title . '">';
        $html[] = '<span class="fa fa-' . $class . '"></span>';
        $html[] = '</a>';

        return implode($html);
    }
}
