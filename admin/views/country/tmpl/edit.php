<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        
            <fieldset>
                <?php echo $this->form->renderField('name'); ?>
                <?php echo $this->form->renderField('code'); ?>
                <?php echo $this->form->renderField('locale'); ?>
                <?php echo $this->form->renderField('latitude'); ?>
                <?php echo $this->form->renderField('longitude'); ?>
                <?php echo $this->form->renderField('currency'); ?>
                <?php echo $this->form->renderField('timezone'); ?>
                <?php echo $this->form->renderField('id'); ?>
            </fieldset>
            
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>