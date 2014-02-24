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
	<div class="span12">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-contact" enctype="multipart/form-data">
            
            <?php echo $this->form->getLabel('phone'); ?>
            <?php echo $this->form->getInput('phone'); ?>
            
            <?php echo $this->form->getLabel('address'); ?>
            <?php echo $this->form->getInput('address'); ?>
            
            <?php echo $this->form->getLabel('location_preview'); ?>
            <?php echo $this->form->getInput('location_preview'); ?>
            
            <?php echo $this->form->getLabel('country_id'); ?>
            <?php echo $this->form->getInput('country_id'); ?>
            
            <?php echo $this->form->getLabel('website'); ?>
            <?php echo $this->form->getInput('website'); ?>
            
            <?php echo $this->form->getInput('location_id'); ?>
            <input type="hidden" name="task" value="contact.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <div class="clearfix"></div>
            <button type="submit" class="btn ">
                <i class="icon-save" ></i>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
</div>