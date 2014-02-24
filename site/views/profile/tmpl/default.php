<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="row-fluid" itemscope itemtype="http://schema.org/Person">
	<div class="span4">
		<?php if(!$this->item->image){?>
		<img src="media/com_socialcommunity/images/no_profile_200x200.png" />
		<?php }else{?>
		<img src="<?php echo $this->imagesFolder."/".$this->item->image;?>" alt="<?php echo $this->item->name;?>" itemprop="image" />
		<?php }?>
		
		<?php if(0 < count($this->socialProfiles)) {?>
		<div class="clearfix">&nbsp;</div>
		<div class="sc-social-profiles">
		  <?php echo JHtml::_("socialcommunity.socialprofiles", $this->socialProfiles, $this->item)?>
		</div>
		<?php }?>
		
		<?php if($this->isOwner){?>
		<div class="clearfix">&nbsp;</div>
		<a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&view=form");?>" class="btn">
		    <i class="icon-edit" ></i>
		    <?php echo JText::_("COM_SOCIALCOMMUNITY_EDIT_PROFILE");?>
	    </a>
		<?php }?>
	</div>
	<div class="span8">
		<h3 itemprop="name"><?php echo $this->item->name;?></h3>
		<?php if(!empty($this->item->bio)) {?>
		<p class="about-bio"><?php echo $this->escape($this->item->bio);?></p>
		<?php }?>
		
		<?php if(!empty($this->displayContactInformation)) {?>
		<h4><?php echo JText::_("COM_SOCIALCOMMUNITY_CONTACT_INFORMATION");?></h4>
		
		<?php if(!empty($this->displayAddress)){?>
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <span itemprop="streetAddress" class="contact-info">
              <?php echo $this->escape($this->item->address);?>,
            </span>
            <span itemprop="addressLocality" class="contact-info"><?php echo $this->escape($this->item->location);?></span>
        </div>
        <?php } ?>
        <?php echo JText::_("COM_SOCIALCOMMUNITY_PHONE");?>: <span itemprop="telephone"><?php echo $this->escape($this->item->phone);?></span>
        <?php }?>
	</div>
</div>
<div class="clearfix">&nbsp;</div>