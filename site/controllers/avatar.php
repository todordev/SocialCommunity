<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Form controller class.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerAvatar extends Prism\Controller\DefaultController
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialcommunityModelAvatar|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Avatar', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
