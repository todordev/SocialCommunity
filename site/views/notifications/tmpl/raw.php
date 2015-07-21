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
<?php foreach ($this->items as $item) {
    $notReadClass = "";
    if (!$item->status) {
        $notReadClass = "sc-note-notread";
    }
    ?>
    <div class="row sc-note-tiny <?php echo $notReadClass; ?>">
        <div class="col-xs-10">
            <a href="<?php echo JRoute::_(SocialCommunityHelperRoute::getNotificationRoute($item->id)); ?>">
                <?php echo $this->escape($item->content); ?>
            </a>
        </div>
        <div class="col-xs-2">
            <img src="<?php echo (!$item->status) ? "media/com_gamification/images/status_active.png" : "media/com_gamification/images/status_inactive.png"; ?>"/>
        </div>
    </div>
<?php } ?>