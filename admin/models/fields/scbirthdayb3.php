<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldScBirthdayB3 extends JFormField
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'scbirthdayb3';

    protected function getInput()
    {
        $class    = !empty($this->class) ? $this->class : '';
        $required = $this->required ? ' required aria-required="true"' : '';

        $birthdayDay   = '';
        $birthdayMonth = '01';
        $birthdayYear  = '';

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
        
        $months = new Socialcommunity\Filter\Months();

        $html = array();

        $html[] = '<div class="' . $class . '">';
        $html[] = '    <input name="' . $this->name . '[day]"   value="' . $birthdayDay . '" id="birthday_day" class="col-md-3 '. $class .'" type="text" placeholder="' . JText::_('COM_SOCIALCOMMUNITY_DAY') . '" ' . $required . '>';
        $html[] = JHTML::_('select.genericlist', $months->toOptions(), $this->name . '[month]', array('class' => 'col-md-3 ' . $class), 'text', 'value', $birthdayMonth, 'birthday_month');
        $html[] = '    <input name="' . $this->name . '[year]"  value="' . $birthdayYear . '" id="birthday_year"  class="col-md-4 '. $class .'" type="text" placeholder="' . JText::_('COM_SOCIALCOMMUNITY_YEAR') . '" ' . $required . '>';
        $html[] = '</div>';

        return implode($html);
    }
}
