<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * SocialCommunity locations controller class.
 *
 * @package        ITPrism Components
 * @subpackage     SocialCommunity
 * @since          1.6
 */
class SocialCommunityControllerLocations extends Prism\Controller\Admin
{
    public function getModel($name = 'Location', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
