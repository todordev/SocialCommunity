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
<div class="sc-notifications<?php echo $this->pageclass_sfx; ?>" id="js-sc-notifications">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php foreach ($this->items as $item) {
        $notReadClass = "";
        if (!$item->status) {
            $notReadClass = "sc-note-notread";
        }
        ?>
        <div class="sc-notification <?php echo $notReadClass; ?> row mtb-5px" id="js-sc-note-element<?php echo $item->id; ?>">
            <div class="col-md-10">
                <div class="media">
                    <?php if (!empty($item->image)) { ?>
                        <div class="media-left">
                            <img class="media-object" src="<?php echo $item->image; ?>">
                        </div>
                    <?php } ?>
                    <div class="media-body">
                        <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getNotificationRoute($item->id)); ?>"><?php echo $item->content; ?></a>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <img src="<?php echo (!$item->status) ? "media/com_socialcommunity/images/status_active.png" : "media/com_socialcommunity/images/status_inactive.png"; ?>"/>
            </div>
            <div class="col-md-1">
                <button data-element-id="<?php echo (int)$item->id; ?>" class="btn btn-danger js-sc-btn-remove-notification">
                    <i class="glyphicon glyphicon-trash"></i>
                </button>
            </div>
        </div>
    <?php } ?>

</div>