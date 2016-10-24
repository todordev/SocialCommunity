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
<div class="sc-profile-registration<?php echo $this->pageclass_sfx?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" data-toggle="tab" href="#signin" role="tab">
                <?php echo JText::_('COM_SOCIALCOMMUNITY_SIGNIN');?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#registration" role="tab">
                <?php echo JText::_('COM_SOCIALCOMMUNITY_REGISTRATION');?>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="signin" role="tabpanel">
            <form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well">

                <fieldset>
                    <legend><?php echo JText::_('COM_SOCIALCOMMUNITY_SIGNIN');?></legend>
                    <div class="form-group">
                        <?php echo $this->formLogin->getLabel('username'); ?>
                        <div class="col-md-10">
                            <?php echo $this->formLogin->getInput('username'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $this->formLogin->getLabel('password'); ?>
                        <div class="col-md-10">
                            <?php echo $this->formLogin->getInput('password'); ?>
                        </div>
                    </div>

                    <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <div class="checkbox">
                                <label>
                                    <input id="remember" type="checkbox" name="remember" value="yes" />
                                    <?php echo JText::_('COM_SOCIALCOMMUNITY_LOGIN_REMEMBER_ME') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn btn-primary">
                                <?php echo JText::_('COM_SOCIALCOMMUNITY_SIGN_IN'); ?>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_(SocialcommunityHelperRoute::getProfileRoute())); ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                </fieldset>
            </form>
            <div>
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="btn btn-link sc-block">
                    <?php echo JText::_('COM_SOCIALCOMMUNITY_LOGIN_RESET'); ?>
                </a>
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn btn-link sc-block">
                    <?php echo JText::_('COM_SOCIALCOMMUNITY_LOGIN_REMIND'); ?>
                </a>
            </div>
        </div>

        <div class="tab-pane" id="registration" role="tabpanel">
            <form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal well" enctype="multipart/form-data">
                <fieldset>
                    <legend><?php echo JText::_('COM_SOCIALCOMMUNITY_USER_REGISTRATION');?></legend>

                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('name'); ?>
                        <?php echo $this->formRegistration->getInput('name'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('username'); ?>
                        <?php echo $this->formRegistration->getInput('username'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('password1'); ?>
                        <?php echo $this->formRegistration->getInput('password1'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('password2'); ?>
                        <?php echo $this->formRegistration->getInput('password2'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('email1'); ?>
                        <?php echo $this->formRegistration->getInput('email1'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('email2'); ?>
                        <?php echo $this->formRegistration->getInput('email2'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->formRegistration->getLabel('captcha'); ?>
                        <?php echo $this->formRegistration->getInput('captcha'); ?>
                    </div>
                </fieldset>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary validate"><?php echo JText::_('JREGISTER');?></button>
                    <a class="btn btn-default" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>">
                        <?php echo JText::_('JCANCEL');?>
                    </a>
                </div>
                <input type="hidden" name="option" value="com_users" />
                <input type="hidden" name="task" value="registration.register" />
                <input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_(SocialcommunityHelperRoute::getProfileRoute())); ?>" />
                <?php echo JHtml::_('form.token');?>
            </form>
            <?php echo $this->formRegistration->getLabel('spacer'); ?>
        </div>
    </div>
</div>