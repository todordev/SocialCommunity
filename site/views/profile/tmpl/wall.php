<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (!$this->item->image) {
    $image       = 'media/com_socialcommunity/images/no_profile_200x200.png';
    $imageAlt    = '';
} else {
    $image       = $this->mediaFolder . '/' . $this->item->image;
    $imageAlt    = 'alt="' . $this->escape($this->item->name) . '"';
}
?>
<div class="scprofile-wall<?php echo $this->params->get('pageclass_sfx'); ?>">
    <?php
    if ($this->item->event->beforeDisplayContent) {
        echo $this->item->event->beforeDisplayContent;
    } ?>

    <div itemscope itemtype="http://schema.org/Person">
        <?php
        if ($this->item->event->beforeDisplayProfileContent) {
            echo $this->item->event->beforeDisplayProfileContent;
        } ?>
        <div class="media">
            <div class="media-left">
                <img src="<?php echo $image; ?>" <?php echo $imageAlt; ?> itemprop="image"/>
            </div>
            <div class="media-body">
                <h3 itemprop="name"><?php echo $this->item->name; ?></h3>
            </div>
        </div>
        <?php
        if ($this->item->event->afterDisplayProfileContent) {
            echo $this->item->event->afterDisplayProfileContent;
        } ?>
    </div>

    <div class="row mt-20">
        <div class="col-md-12 sc-wall-wrapper well">
            <form method="post" action="<?php echo JRoute::_(''); ?>" id="js-sc-wall-form" class="sc-wall-form">
                <label class="sr-only" for="sc-wall-textarea"><?php echo JText::_('COM_SOCIALCOMMUNITY_SHARE_SOMETHING'); ?></label>
                <textarea name="content" class="col-md-12" id="sc-wall-textarea" placeholder="<?php echo JText::_('COM_SOCIALCOMMUNITY_SHARE_SOMETHING_PLACEHOLDER'); ?>"></textarea>

                <div class="clearfix"></div>
                <div class="row ">
                    <div class="col-md-8">

                    </div>
                    <div class="col-md-1">
                        <div id="js-sc-wall-counter">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary pull-right btn-block" type="submit">
                            <?php echo JText::_('COM_SOCIALCOMMUNITY_SHARE'); ?>
                        </button>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
            <div id="js-sc-wall-posts" class="list-group mt-20">
                <wallpost
                    v-for="(post, index) in posts"
                    v-bind:post="post"
                    v-bind:profile="profile"
                    v-bind:index="index"
                    v-bind:key="post.id"
                ></wallpost>
            </div>
        </div>
    </div>
    <?php
    if ($this->item->event->afterDisplayContent) {
        echo $this->item->event->afterDisplayContent;
    } ?>
</div>

<div id="js-edit-post-modal">
    <form action="index.php" method="post" name="edit_post">
        <div class="form-group">
            <label for="js-post-editor" class="hide"><?php echo JText::_('COM_SOCIALCOMMUNITY_YOUR_POST'); ?></label>
            <textarea class="form-control" rows="3" id="js-post-editor"></textarea>
        </div>

        <div class="form-group mtr-10-10 pull-right">
            <button class="btn btn-primary" type="button" id="js-edit-post-btn-submit"><?php echo JText::_('COM_SOCIALCOMMUNITY_SUBMIT'); ?></button>
            <button class="btn btn-default" type="button" id="js-edit-post-btn-cancel"><?php echo JText::_('COM_SOCIALCOMMUNITY_CANCEL'); ?></button>
        </div>
    </form>
</div>