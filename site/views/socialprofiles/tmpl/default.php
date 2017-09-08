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
        <?php if ($this->facebookEnabled) {?>
        <div class="form-group">
            <?php echo $this->form->getLabel('facebook'); ?>
            <div class="row">
                <div class="col-md-9">
                    <?php echo $this->form->getInput('facebook'); ?>
                </div>
                <div class="col-md-3">
                    <?php if ($this->facebookDisconnectUrl) { ?>
                        <a href="<?php echo $this->facebookDisconnectUrl; ?>" class="btn btn-danger" role="button">
                            <span class="fa fa-ban"></span>
                            <?php echo JText::_('COM_SOCIALCOMMUNITY_DISCONNECT_FACEBOOK'); ?>
                        </a>
                    <?php } else { ?>
                    <a href="<?php echo $this->facebookLoginUrl; ?>" class="btn btn-primary" role="button">
                        <span class="fa fa-link"></span>
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_CONNECT_FACEBOOK'); ?>
                    </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ($this->twitterEnabled) {?>
        <div class="form-group">
            <?php echo $this->form->getLabel('twitter'); ?>
            <div class="row">
                <div class="col-md-9">
                    <?php echo $this->form->getInput('twitter'); ?>
                </div>
                <div class="col-md-3">
                    <?php if (!$this->twitterLoginUrl) { ?>
                        <a href="<?php echo $this->twitterDisconnectUrl; ?>" class="btn btn-danger" role="button">
                            <span class="fa fa-ban"></span>
                            <?php echo JText::_('COM_SOCIALCOMMUNITY_DISCONNECT_TWITTER'); ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo $this->twitterLoginUrl; ?>" class="btn btn-primary" role="button">
                            <span class="fa fa-link"></span>
                            <?php echo JText::_('COM_SOCIALCOMMUNITY_CONNECT_TWITTER'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ($this->googlePlusEnabled) {?>
        <div class="form-group">
            <?php echo $this->form->getLabel('googleplus'); ?>
            <div class="row">
                <div class="col-md-9">
                    <?php echo $this->form->getInput('googleplus'); ?>
                </div>
                <div class="col-md-3">
                    <?php
                    $gpBtnSignOut = $this->googlePlusConnected ? 'block' : 'none';
                    $gpBtnSignIn  = $this->googlePlusConnected ? 'none' : 'block';
                    ?>
                    <button id="js-googleplus-signout" class="btn btn-danger" type="button" style="display: <?php echo $gpBtnSignOut; ?>">
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_DISCONNECT_GOOGLEPLUS'); ?>
                    </button>
                    <button id="js-googleplus-signin" class="btn btn-primary" type="button" style="display: <?php echo $gpBtnSignIn; ?>">
                        <?php echo JText::_('COM_SOCIALCOMMUNITY_CONNECT_GOOGLEPLUS'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>