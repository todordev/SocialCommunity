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
<div class="sc-notifications<?php echo $this->pageclass_sfx; ?>" id="js-sc-notifications">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php foreach ($this->items as $item) {
        $notReadClass = '';
        if (!$item->status) {
            $notReadClass = 'sc-note-notread';
        }
        ?>
        <div class="panel panel-default" id="js-sc-note-element<?php echo $item->id; ?>">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 gray-light4">
                        <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2')); ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-11">
                        <div class="media">
                            <?php if (!empty($item->image)) { ?>
                                <div class="media-left">
                                    <img class="media-object" src="<?php echo $item->image; ?>">
                                </div>
                            <?php } ?>
                            <div class="media-body">
                                <a href="<?php echo JRoute::_(SocialcommunityHelperRoute::getNotificationRoute($item->id)); ?>"><?php echo $item->content; ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <img src="<?php echo (!$item->status) ? 'media/com_socialcommunity/images/status_active.png' : 'media/com_socialcommunity/images/status_inactive.png'; ?>"/>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-2">
                        <button data-element-id="<?php echo (int)$item->id; ?>" class="btn btn-xs btn-danger js-sc-btn-remove-notification">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    <?php } ?>

</div>