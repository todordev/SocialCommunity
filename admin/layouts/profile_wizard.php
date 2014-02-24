<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

$active = array("basic" => false, "contact" => false, "social" => false);

switch($displayData->layout) {
    case "default":
        $active["basic"]  = true;
        break;
    case "contact":
        $active["contact"] = true;
        break;
    case "social":
        $active["social"] = true;
        break;
}

?>
<div class="navbar">
    <div class="navbar-inner">
    	<a class="brand" href="javascript:void(0);"><?php echo JText::_("COM_SOCIALCOMMUNITY_PROFILE");?></a>

    	<ul class="nav">
            <li <?php echo ($active["basic"]) ? 'class="active"' : '';?>>
            	<a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute("default"));?>">
            	<?php echo JText::_("COM_SOCIALCOMMUNITY_BASIC");?>
            	</a>
            </li>
            
            <li <?php echo ($active["contact"]) ? 'class="active"' : '';?>>
                <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute("contact"));?>">
                    <?php echo JText::_("COM_SOCIALCOMMUNITY_CONTACT");?>
                </a>
            </li>
            
            <li <?php echo ($active["social"]) ? 'class="active"' : '';?>>
                <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getFormRoute("social"));?>">
                    <?php echo JText::_("COM_SOCIALCOMMUNITY_SOCIAL");?>
                </a>
            </li>
            
        </ul>
     </div>
</div>
