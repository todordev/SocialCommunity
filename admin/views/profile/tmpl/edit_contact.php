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

echo $this->form->renderField('phone');
echo $this->form->renderField('address');
echo $this->form->renderField('country_code');
echo $this->form->renderField('location_preview');
echo $this->form->renderField('website');
