<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_COUNTRY_CODE4', 'a.code4', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_COUNTRY_LATITUDE', 'a.latitude', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_COUNTRY_LONGITUDE', 'a.longitude', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="center nowrap hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_SOCIALCOMMUNITY_COUNTRY_TIMEZONE', 'a.timezone', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  