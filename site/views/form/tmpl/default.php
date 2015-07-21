<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>

<div class="row">
	<div class="col-md-12">
		<?php 
    		$layout      = new JLayoutFile('profile_wizard', $this->layoutsBasePath);
    	    echo $layout->render($this->layoutData);
		?>	
	</div>
</div>

<div class="row">
	<div class="col-md-8">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-basic" enctype="multipart/form-data">

            <div class="form-group">
            <?php echo $this->form->getLabel('name'); ?>
            <?php echo $this->form->getInput('name'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('bio'); ?>
            <?php echo $this->form->getInput('bio'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('birthday'); ?>
            <?php echo $this->form->getInput('birthday'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('gender'); ?>
            <?php echo $this->form->getInput('gender'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('photo'); ?>
            <?php echo $this->form->getInput('photo'); ?>
            </div>
            
            <input type="hidden" name="task" value="basic.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-ok" ></span>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
    <div class="col-md-4">
    <?php if(!empty($this->item["image"])) {?>
    	<img src="<?php echo $this->imagesFolder."/".$this->item["image"];?>" class="img-thumbnail" />
        <br />
    	<a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&task=basic.removeImage&".JSession::getFormToken()."=1");?>" class="btn btn-mini btn-danger mt-10" id="js-btn-remove-image">
    	   <span class="glyphicon glyphicon-trash"></span>
    	   <?php echo JText::_("COM_SOCIALCOMMUNITY_REMOVE_IMAGE");?>
	    </a>
    <?php } else {?>
        <img src="media/com_socialcommunity/images/no_profile_200x200.png" class="img-thumbnail" />
    <?php }?>
    </div>

</div>