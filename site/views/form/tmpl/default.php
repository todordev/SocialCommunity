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
                <div class="clearfix"></div>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('gender'); ?>
            <?php echo $this->form->getInput('gender'); ?>
            </div>
            
            <input type="hidden" name="task" value="basic.save" />
            <?php echo JHtml::_('form.token'); ?>
                
            <button type="submit" class="btn btn-primary">
                <span class="fa fa-check" ></span>
                <?php echo JText::_('JSAVE')?>
            </button>
            
        </form>
    </div>
</div>