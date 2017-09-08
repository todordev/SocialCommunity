<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>

<div class="row">
    <div class="col-md-12">
        <?php
        $layout = new JLayoutFile('profile_wizard');
        echo $layout->render($this->layoutData);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php
        $buttonStyle = 'style="display: none"';
        $image       = 'media/com_socialcommunity/images/no_profile_200x200.png';
        if ($this->item->image) {
            $buttonStyle = '';
            $image       = $this->mediaFolder . '/' . $this->item->image;
        }
        ?>
        <img src="<?php echo $image; ?>" class="img-thumbnail center-block" id="js-avatar-img"/>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="btn btn-default fileinput-button">
            <span class="fa fa-upload"></span>
            <span><?php echo JText::_('COM_SOCIALCOMMUNITY_UPLOAD_IMAGE'); ?></span>
            <!-- The file input field used as target for the file upload widget -->
            <input id="js-thumb-fileupload" type="file" name="profile_image" data-url="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>"/>
        </span>

        <button type="button" <?php echo $buttonStyle; ?> class="btn btn-danger" id="js-btn-remove-image">
            <span class="fa fa-trash"></span>
            <?php echo JText::_('COM_SOCIALCOMMUNITY_REMOVE_IMAGE'); ?>
        </button>

        <span class="fa fa-spinner fa-spin fa-fw" id="js-avatar-loader" style="display: none;" ></span>

        <span class="btn hasPopover ml-10" data-content="<?php echo JText::sprintf('COM_SOCIALCOMMUNITY_MEDIA_FILES_ALLOWED_S', $this->imageWidth, $this->imageHeight, $this->maxFilesize);?>">
            <span class="fa fa-question-circle" title="" ></span>
        </span>
    </div>
</div>

<div id="js-modal-wrapper">
    <div id="js-fixed-dragger-cropper">
        <img src="" id="js-cropper-img" />
    </div>

    <div class="mt-10">
        <a href="javascript: void(0);" class="btn btn-primary" id="js-crop-btn">
            <span class="fa fa-check-circle"></span>
            <?php echo JText::_('COM_SOCIALCOMMUNITY_CROP_IMAGE');?>
        </a>

        <a href="javascript: void(0);" class="btn btn-default" id="js-crop-btn-cancel">
            <span class="fa fa-ban"></span>
            <?php echo JText::_('COM_SOCIALCOMMUNITY_CANCEL');?>
        </a>

        <span class="fa fa-spinner fa-spin" id="js-modal-loader" style="display: none;" aria-hidden="true"></span>
    </div>
</div>

<input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" id="js-form-token"/>