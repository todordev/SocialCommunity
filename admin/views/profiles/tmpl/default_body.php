<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $profilePicture = $item->image_icon ? $this->filesystemHelper->getMediaFolderUri($item->user_id) . '/' . $item->image_icon : '../media/com_socialcommunity/images/no_profile_24x24.png';
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_socialcommunity&view=profile&layout=edit&id=' . $item->id); ?>">
                <?php echo $this->escape($item->name); ?>
            </a>
            <div class="small"><?php echo JText::sprintf('COM_SOCIALCOMMUNITY_ALIAS_S', $item->alias); ?></div>
        </td>
        <td class="nowrap center hidden-phone">
            <img src="<?php echo $profilePicture; ?>"/>
        </td>
        <td class="nowrap center hidden-phone">
            <?php if (isset($item->socialProfiles)) {
               echo JHtml::_('socialcommunitybackend.socialprofiles', $item->socialProfiles);
            }?>
        </td>
        <td class="nowrap hidden-phone">
            <?php echo $this->escape($item->country); ?>
        </td>

        <td class="nowrap hidden-phone">
            <?php echo $item->registerDate; ?>
        </td>
        <td class="nowrap center hidden-phone">
            <?php echo $item->user_id; ?>
        </td>
        <td class="nowrap center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>