<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$active = array('basic' => false, 'avatar' => false, 'contact' => false, 'social' => false);

switch($displayData->layout) {
    case 'default':
        $active['basic']  = true;
        break;
    case 'avatar':
        $active['avatar'] = true;
        break;
    case 'contact':
        $active['contact'] = true;
        break;
    case 'social':
        $active['social'] = true;
        break;
}
?>
<div class="navbar navbar-default sc-profile-navigation" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
    	    <a class="navbar-brand" href="javascript:void(0);"><?php echo JText::_('COM_SOCIALCOMMUNITY_PROFILE'); ?></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li <?php echo ($active['basic']) ? 'class="active"' : '';?>>
                    <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute('default'));?>">
                    <?php echo JText::_('COM_SOCIALCOMMUNITY_BASIC'); ?>
                    </a>
                </li>

                <li <?php echo ($active['avatar']) ? 'class="active"' : '';?>>
                    <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute('avatar'));?>">
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_PICTURE'); ?>
                    </a>
                </li>

                <li <?php echo ($active['contact']) ? 'class="active"' : '';?>>
                    <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute('contact'));?>">
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_CONTACT'); ?>
                    </a>
                </li>

                <li <?php echo ($active['social']) ? 'class="active"' : '';?>>
                    <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute('social'));?>">
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_SOCIAL_PROFILES'); ?>
                    </a>
                </li>
            </ul>
        </div>
     </div>
</div>
