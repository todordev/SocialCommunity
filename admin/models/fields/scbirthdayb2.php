<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      Socialcommunity
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldScBirthdayB2 extends JFormField
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'scbirthdayb2';

    protected function getInput()
    {
        $birthdayDay = '';

        $class    = !empty($this->class) ? $this->class : '';
        $required = $this->required ? ' required aria-required="true"' : '';

        // Prepare birthday
        if (!empty($this->value)) {
            $date = new Prism\Validator\Date($this->value);

            if (!$date->isValid()) {
                $birthdayDay   = '';
                $birthdayMonth = '';
                $birthdayYear  = '';
            } else {
                $date = new JDate($this->value);

                $birthdayDay   = $date->format('d');
                $birthdayMonth = $date->format('m');
                $birthdayYear  = $date->format('Y');
            }
        }

        $months = Socialcommunity\Helper\Joomla::monthsToOptions();
        
        $html = array();
        $html[] = '<div class="controls controls-row ' . $class . '">';
        $html[] = '    <input name="' . $this->name . '[day]"   value="' . $birthdayDay . '" id="birthday_day"   class="span3" type="text" placeholder="' . JText::_('COM_SOCIALCOMMUNITY_DAY') . '" ' . $required . '>';
        $html[] = JHTML::_('select.genericlist', $months, $this->name . '[month]', array('class' => 'span3 ' . $class), 'text', 'value', $birthdayMonth, 'birthday_month');
        $html[] = '    <input name="' . $this->name . '[year]"  value="' . $birthdayYear . '" id="birthday_year"  class="span4" type="text" placeholder="' . JText::_('COM_SOCIALCOMMUNITY_YEAR') . '" ' . $required . '>';
        $html[] = '</div>';

        return implode($html);
    }
}
