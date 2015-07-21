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
	<div class="col-md-12">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-contact">

            <div class="form-group">
                <?php echo $this->form->getLabel('phone'); ?>
                <?php echo $this->form->getInput('phone'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('address'); ?>
            <?php echo $this->form->getInput('address'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('location_preview'); ?>
            <?php echo $this->form->getInput('location_preview'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('country_id'); ?>
            <?php echo $this->form->getInput('country_id'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('website'); ?>
            <?php echo $this->form->getInput('website'); ?>
            </div>

            <?php echo $this->form->getInput('location_id'); ?>

            <input type="hidden" name="task" value="contact.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-ok" ></span>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
</div>