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
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('address'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('location_preview'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('location_preview'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('country_id'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('country_id'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('website'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('website'); ?></div>
</div>