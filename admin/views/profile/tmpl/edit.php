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
?>
<form  action="<?php echo JRoute::_('index.php?option=com_socialcommunity&layout=edit'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data" autocomplete="off">
    
    <div class="form-horizontal">
    
        <?php echo JHtml::_('bootstrap.startTabSet', 'profile-data', array('active' => 'basic')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'profile-data', 'basic', JText::_('COM_SOCIALCOMMUNITY_BASIC')); ?>
        <div class="row-fluid">
            <div class="span8">
                <?php echo $this->loadTemplate("basic");?>
           </div>
           <div class="span4">
            <?php if(!empty($this->item->image)) {?>
            	<img src="<?php echo $this->imagesFolder."/".$this->item->image;?>" class="img-polaroid" />

                <div class="clearfix"></div>
                <a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&task=profile.removeImage&".JSession::getFormToken()."=1&id=".(int)$this->item->id);?>" class="btn btn-mini btn-danger">
                    <i class="icon-trash"></i>
                    <?php echo JText::_("COM_SOCIALCOMMUNITY_REMOVE_IMAGE");?>
                </a>

            <?php } else {?>
                <img src="../media/com_socialcommunity/images/no_profile_200x200.png" class="img-polaroid" />
            <?php }?>
           </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'profile-data', 'contact', JText::_('COM_SOCIALCOMMUNITY_CONTACT')); ?>
        <div class="row-fluid">
            <div class="span12">
            <?php echo $this->loadTemplate("contact");?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'profile-data', 'social', JText::_('COM_SOCIALCOMMUNITY_SOCIAL')); ?>
        <div class="row-fluid">
            <div class="span12">
            <?php echo $this->loadTemplate("social");?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.endTabSet');?>
        
    </div>

    <?php echo $this->form->getInput('location_id'); ?>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>