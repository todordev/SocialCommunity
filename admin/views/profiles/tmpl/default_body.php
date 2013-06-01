<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'b.registerDate');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td>
		<?php if(!empty($item->profile_id)) {?>
		<a href="<?php echo JRoute::_("index.php?option=com_socialcommunity&view=profile&layout=edit&id=".$item->id);?>"><?php echo $item->name; ?></a>
		<?php } else {?>
		<?php echo $item->name; ?>
		<?php }?>
		</td>
		<td class="center">
			<?php if(!empty($item->image_icon)) {?>
			<img src="<?php echo "../".$this->imagesFolder."/".$item->image_icon; ?>" />
			<?php } else {?>
			<img src="<?php echo "../media/com_socialcommunity/images/no-profile_24.png"; ?>" />
			<?php }?>
		</td>
		<td class="center"><?php echo $item->registerDate; ?></td>
		<td class="center"><?php echo JHtml::_('socialcommunity.profileexists', $i, "profiles.create", $item->profile_id, array("tooltip"=>true)); ?></td>
        <td class="center"><?php echo JHtml::_('grid.boolean', $i, !$item->block); ?></td>
        <td class="center"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  