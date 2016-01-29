<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
	<div class="col-md-12">
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-social">

            <div class="form-group">
            <?php echo $this->form->getLabel('facebook'); ?>
            <?php echo $this->form->getInput('facebook'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('twitter'); ?>
            <?php echo $this->form->getInput('twitter'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('linkedin'); ?>
            <?php echo $this->form->getInput('linkedin'); ?>
            </div>

            <input type="hidden" name="task" value="social.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <div class="clearfix"></div>
            <button type="submit" class="btn btn-primary">
                <span class="fa fa-check" ></span>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
</div>