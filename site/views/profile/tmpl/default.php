<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="row-fluid">
	<div class="span3">
		<?php if(!$this->item["image"]){?>
		<img src="media/com_socialcommunity/images/no-profile.png" />
		<?php }else{?>
		<img src="<?php echo $this->imagesFolder.$this->item["image"];?>" alt="<?php echo $this->item["name"];?>" />
		<?php }?>
		<?php if($this->isOwner){?>
		<div class="clearfix">&nbsp;</div>
		<a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&view=form");?>" class="btn"><?php echo JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE");?></a>
		<?php }?>
	</div>
	<div class="span9">
		<h3><?php echo $this->item["name"];?></h3>
		<p class="about-bio"><?php echo $this->escape($this->item["bio"]);?></p>
	</div>
</div>
<div class="clearfix">&nbsp;</div>