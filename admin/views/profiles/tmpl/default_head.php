<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
	<th class="title" >
	     <?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_NAME', 'a.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%" class="nowrap center hidden-phone">&nbsp;</th>
	<th width="10%" class="nowrap hidden-phone">
	    <?php echo JText::_("COM_SOCIALCOMMUNITY_COUNTRY"); ?>
	</th>
	<th width="10%" class="nowrap center hidden-phone">
	     <?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_REGISTERED', 'b.registerDate', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_USER_ID', 'a.user_id', $this->listDirn, $this->listOrder); ?>
	</th>
    <th width="1%" class="nowrap center hidden-phone">
         <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  