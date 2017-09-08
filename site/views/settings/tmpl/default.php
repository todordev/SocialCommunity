<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="scsettings<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <form action="<?php echo JRoute::_('index.php?option=com_socialcommunity'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'scSettings', array('active' => 'privacy')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'scSettings', 'privacy', JText::_('COM_SOCIALCOMMUNITY_PRIVACY')); ?>
        <div class="row">
            <div class="form-group">
                <?php echo $this->form->getLabel('picture', 'privacy'); ?>
                <div class="col-md-10">
                    <?php echo $this->form->getInput('picture', 'privacy'); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $this->form->getLabel('bio', 'privacy'); ?>
                <div class="col-md-10">
                    <?php echo $this->form->getInput('bio', 'privacy'); ?>
                </div>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'scSettings', 'account', JText::_('COM_SOCIALCOMMUNITY_ACCOUNT')); ?>
        <div class="row">
            <div class="form-group">
                <?php echo $this->form->getLabel('account_state', 'account'); ?>
                <div class="col-md-10">
                    <?php echo $this->form->getInput('account_state', 'account'); ?>
                </div>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="settings.save"/>
        <?php echo JHtml::_('form.token'); ?>

        <button type="submit" class="btn btn-primary">
            <span class="fa fa-check" ></span>
            <?php echo JText::_('JSAVE')?>
        </button>
    </form>
</div>