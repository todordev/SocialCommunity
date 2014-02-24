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

<div class="row-fluid">
	<div class="span12">
		<?php 
    		$layout      = new JLayoutFile('profile_wizard', $this->layoutsBasePath);
    	    echo $layout->render($this->layoutData);
		?>	
	</div>
</div>

<div class="row-fluid">
	<div class="span8">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-basic" enctype="multipart/form-data">
            
            <?php echo $this->form->getLabel('name'); ?>
            <?php echo $this->form->getInput('name'); ?>
            
            <?php echo $this->form->getLabel('bio'); ?>
            <?php echo $this->form->getInput('bio'); ?>
            
            <?php echo $this->form->getLabel('birthday'); ?>
            <?php echo $this->form->getInput('birthday'); ?>
            
            <?php echo $this->form->getLabel('gender'); ?>
            <?php echo $this->form->getInput('gender'); ?>
            
            <?php echo $this->form->getLabel('photo'); ?>
            
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <span class="btn btn-file">
                    <i class="icon-upload"></i> 
                    <span class="fileupload-new"><?php echo JText::_("COM_SOCIALCOMMUNITY_SELECT_FILE");?></span>
                    <span class="fileupload-exists"><?php echo JText::_("COM_SOCIALCOMMUNITY_CHANGE");?></span>
                    <?php echo $this->form->getInput('photo'); ?>
                </span>
                <span class="fileupload-preview"></span>
                <a href="javascript: void(0);" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
            </div>
            
            <input type="hidden" name="task" value="basic.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <div class="clearfix"></div>
            <button type="submit" class="btn ">
                <i class="icon-save" ></i>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
    <div class="span4">
    <?php if(!empty($this->item["image"])) {?>
    	<img src="<?php echo $this->imagesFolder."/".$this->item["image"];?>" class="img-polaroid" />
    	<div class="clearfix"></div>
    	<a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&task=basic.removeImage&".JSession::getFormToken()."=1");?>" class="btn btn-mini btn-danger">
    	   <i class="icon-trash"></i> 
    	   <?php echo JText::_("COM_SOCIALCOMMUNITY_REMOVE_IMAGE");?>
	    </a>
    <?php } else {?>
        <img src="media/com_socialcommunity/images/no_profile_200x200.png" class="img-polaroid" />
    <?php }?>
    </div>

</div>