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
        <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" id="itpsc-form-social">
            
            <?php echo $this->form->getLabel('facebook'); ?>
            <?php echo $this->form->getInput('facebook'); ?>
            
            <?php echo $this->form->getLabel('twitter'); ?>
            <?php echo $this->form->getInput('twitter'); ?>
            
            <?php echo $this->form->getLabel('linkedin'); ?>
            <?php echo $this->form->getInput('linkedin'); ?>
            
            <input type="hidden" name="task" value="social.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <div class="clearfix"></div>
            <button type="submit" class="btn ">
                <i class="icon-save" ></i>
                <?php echo JText::_("JSAVE")?>
            </button>
            
        </form>
    </div>
    
</div>