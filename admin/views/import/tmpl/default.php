<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        
            <fieldset>
                <legend><?php echo $this->legend; ?></legend>
                
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('data'); ?></div>
    				<div class="controls">
    				
        				<div class="fileupload fileupload-new" data-provides="fileupload">
                        <span class="btn btn-file">
                            <span class="fileupload-new"><?php echo JText::_("COM_SOCIALCOMMUNITY_SELECT_FILE");?></span>
                            <span class="fileupload-exists"><?php echo JText::_("COM_SOCIALCOMMUNITY_CHANGE");?></span>
                            <?php echo $this->form->getInput('data'); ?>
                        </span>
                        <span class="fileupload-preview"></span>
                        <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                        </div>
    				</div>
                </div>
                
                <?php if(strcmp($this->importType, "states") != 0) {?>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('reset_id'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('reset_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('remove_old'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('remove_old'); ?></div>
                </div>
                <?php }?>

                <?php if (strcmp($this->importType, "locations") == 0) { ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('minimum_population'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('minimum_population'); ?></div>
                    </div>
                <?php } ?>

    		</fieldset>
            
            <input type="hidden" name="task" value="" id="task"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>