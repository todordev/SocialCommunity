<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Socialcommunity Profiles Controller
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerProfiles extends Prism\Controller\Admin
{
    public function getModel($name = 'Profile', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
