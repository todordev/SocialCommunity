<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
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
        $class    = !empty($this->class) ? $this->class : '';
        $required = $this->required ? ' required aria-required="true"' : '';

        // Prepare birthday
        if (!empty($this->value)) {

            $date = new Prism\Validator\Date($this->value);

            if (!$date->isValid()) {
                $birthdayDay   = "";
                $birthdayMonth = "";
                $birthdayYear  = "";
            } else {
                $date = new JDate($this->value);

                $birthdayDay   = $date->format("d");
                $birthdayMonth = $date->format("m");
                $birthdayYear  = $date->format("Y");
            }

        }

        $html = array();

        $html[] = '<div class="controls controls-row ' . $class . '">';
        $html[] = '    <input name="' . $this->name . '[day]"   value="' . $birthdayDay . '" id="birthday_day"   class="span3" type="text" placeholder="' . JText::_("COM_SOCIALCOMMUNITY_DAY") . '" ' . $required . '>';
        $html[] = '    <input name="' . $this->name . '[month]" value="' . $birthdayMonth . '" id="birthday_month" class="span3" type="text" placeholder="' . JText::_("COM_SOCIALCOMMUNITY_MONTH") . '" ' . $required . '>';
        $html[] = '    <input name="' . $this->name . '[year]"  value="' . $birthdayYear . '" id="birthday_year"  class="span4" type="text" placeholder="' . JText::_("COM_SOCIALCOMMUNITY_YEAR") . '" ' . $required . '>';
        $html[] = '</div>';

        return implode($html);
    }
}
