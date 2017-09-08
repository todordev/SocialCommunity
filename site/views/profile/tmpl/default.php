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
<div class="scprofile-details<?php echo $this->params->get('pageclass_sfx'); ?>">
    <?php
    if ($this->item->event->beforeDisplayContent) {
        echo $this->item->event->beforeDisplayContent;
    }?>

    <div class="row" itemscope itemtype="http://schema.org/Person">
        <div class="col-md-3">
            <h3 itemprop="name"><?php echo $this->item->name;?></h3>
            <?php
            if (!$this->item->params->get('privacy.picture', Prism\Constants::ACCESS_PUBLIC) or !$this->item->image) {?>
                <img src="media/com_socialcommunity/images/no_profile_200x200.png" />
            <?php } else {?>
                <img src="<?php echo $this->mediaFolder.'/'.$this->item->image;?>" alt="<?php echo $this->escape($this->item->name);?>" itemprop="image" />
            <?php }?>

            <?php
            if ($this->item->event->beforeDisplayProfileContent) {
                echo $this->item->event->beforeDisplayProfileContent;
            } ?>

        </div>
        <div class="col-md-9">
            <?php if ($this->item->params->get('privacy.bio', Prism\Constants::ACCESS_PUBLIC) and $this->item->bio !== '') {?>
            <p class="about-bio"><?php echo $this->escape($this->item->bio);?></p>
            <?php }?>

            <?php
            if ($this->item->event->afterDisplayProfileContent) {
                echo $this->item->event->afterDisplayProfileContent;
            } ?>

        </div>
    </div>

    <?php
    if ($this->item->event->afterDisplayContent) {
        echo $this->item->event->afterDisplayContent;
    } ?>
</div>