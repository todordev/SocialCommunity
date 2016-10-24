<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<?php foreach ($this->items as $item) {
    $notReadClass = '';
    if (!$item->status) {
        $notReadClass = 'sc-note-notread';
    }
    ?>
    <div class="row sc-note-tiny <?php echo $notReadClass; ?>">
        <div class="col-xs-10">
            <a href="<?php echo JRoute::_(SocialcommunityHelperRoute::getNotificationRoute($item->id)); ?>">
                <?php echo $this->escape($item->content); ?>
            </a>
        </div>
        <div class="col-xs-2">
            <img src="<?php echo (!$item->status) ? 'media/com_socialcommunity/images/status_active.png' : 'media/com_socialcommunity/images/status_inactive.png'; ?>"/>
        </div>
    </div>
<?php } ?>