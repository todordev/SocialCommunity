<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * SocialCommunity locations controller class.
 *
 * @package        ITPrism Components
 * @subpackage     SocialCommunity
 * @since          1.6
 */
class SocialCommunityControllerLocations extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Location', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
