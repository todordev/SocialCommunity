<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Socialcommunity countries controller class.
 *
 * @package        Socialcommunity
 * @subpackage     Components
 * @since          1.6
 */
class SocialcommunityControllerCountries extends Prism\Controller\Admin
{
    public function getModel($name = 'Country', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
