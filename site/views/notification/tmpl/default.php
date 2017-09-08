<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="gfy-notification-view<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if ($this->item) { ?>
        <div class="media gfy-notification">
            <?php if (!empty($this->item->getImage())) { ?>
            <div class="media-left">
                <img class="media-object" src="<?php echo $this->item->getImage(); ?>">
            </div>
            <?php } ?>
            <div class="media-body">
                <p><?php echo $this->escape($this->item->getContent()); ?></p>
            </div>
        </div>
    <?php } ?>
</div>