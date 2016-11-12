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
	<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
	<th class="title">
        <?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_NAME', 'a.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_COUNTRY_CODE', 'a.code', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_LOCALE', 'a.locale', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_LATITUDE', 'a.latitude', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_LONGITUDE', 'a.longitude', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_TIMEZONE', 'a.timezone', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>