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
    <div class="control-label"><?php echo $this->form->getLabel('facebook'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('facebook'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('twitter'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('twitter'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('linkedin'); ?></div>
	<div class="controls"><?php echo $this->form->getInput('linkedin'); ?></div>
</div>