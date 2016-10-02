<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>

<div class="row">
	<div class="col-md-12">
		<?php 
    		$layout      = new JLayoutFile('profile_wizard');
    	    echo $layout->render($this->layoutData);
		?>	
	</div>
</div>

<div class="row">
	<div class="col-md-6">
        <?php if ($this->item !== null and $this->item->image) {
            $removeButtonDisplay = '';
            ?>
            <img src="<?php echo $this->mediaFolder.'/'.$this->item->image; ?>" class="img-thumbnail center-block" id="js-avatar-img" />
        <?php } else {
            $removeButtonDisplay = 'style="display: none"';
            ?>
            <img src="media/com_socialcommunity/images/no_profile_200x200.png" class="img-thumbnail center-block" id="js-avatar-img"/>
        <?php }?>

        <a href="<?php echo JRoute::_('index.php?option=com_socialcommunity&task=avatar.removeImage&'.JSession::getFormToken().'=1');?>" <?php echo $removeButtonDisplay; ?> class="btn btn-mini btn-danger mt-10 center-block" id="js-btn-remove-image">
            <span class="fa fa-trash"></span>
            <?php echo JText::_('COM_SOCIALCOMMUNITY_REMOVE_IMAGE');?>
        </a>
    </div>
    
    <div class="col-md-6">

        <div class="mb-15">
            <span class="btn btn-default fileinput-button">
                <span class="fa fa-upload"></span>
                <span><?php echo JText::_('COM_SOCIALCOMMUNITY_UPLOAD_IMAGE');?></span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="js-thumb-fileupload" type="file" name="profile_image" data-url="<?php echo JRoute::_('index.php?option=com_socialcommunity&task=avatar.upload&format=raw');?>" />
            </span>

            <a href="<?php echo JRoute::_('index.php?option=com_socialcommunity&task=avatar.removeImage&id='.$this->item->id.'&'.JSession::getFormToken().'=1');?>" id="js-btn-remove-image" class="btn btn-danger" style="display: <?php echo $this->displayRemoveButton; ?>">
                <span class="fa fa-trash"></span>
                <?php echo JText::_('COM_SOCIALCOMMUNITY_REMOVE_IMAGE');?>
            </a>

            <img src="media/com_socialcommunity/images/ajax-loader.gif" width="16" height="16" id="js-thumb-fileupload-loader" style="display: none;" />

            <div id="js-image-tools" class="mt-10" style="display: none;">
                <button class="btn btn-primary" id="js-crop-btn" type="button">
                    <span class="fa fa-check-circle"></span>
                    <?php echo JText::_('COM_SOCIALCOMMUNITY_CROP_IMAGE');?>
                </button>

                <button class="btn btn-default" id="js-crop-btn-cancel" type="button">
                    <span class="fa fa-ban"></span>
                    <?php echo JText::_('COM_SOCIALCOMMUNITY_CANCEL');?>
                </button>
            </div>

        </div>
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity');?>" method="post" id="js-image-tools-form">
            <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
        </form>
        
    </div>
</div>

<div>
    <div class="col-md-12">
        <div id="js-fixed-dragger-cropper" class="center-block mt-10">
            <?php if ($this->fileForCropping) {?>
            <img src="<?php echo $this->fileForCropping; ?>" class="img-polaroid center-block" />
            <?php } ?>
        </div>
    </div>
</div>