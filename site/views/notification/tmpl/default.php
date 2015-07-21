<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="gfy-notification-view<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if (!empty($this->item)) { ?>
        <div class="media gfy-notification">
            <?php if (!empty($this->item->image)) { ?>
            <div class="media-left">
                <img class="media-object" src="<?php echo $this->item->image; ?>">
            </div>
            <?php } ?>
            <div class="media-body">
                <p><?php echo $this->escape($this->item->content); ?></p>
            </div>
        </div>
    <?php } ?>
</div>