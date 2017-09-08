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
<?php echo $this->form->renderField('name'); ?>
<?php echo $this->form->renderField('alias'); ?>
<?php echo $this->form->renderField('bio'); ?>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('birthday'); ?></div>
    <?php echo $this->form->getInput('birthday'); ?>
</div>
<?php echo $this->form->renderField('gender'); ?>
<?php echo $this->form->renderField('id'); ?>
<?php echo $this->form->renderField('user_id'); ?>
<?php echo $this->form->renderField('photo'); ?>
