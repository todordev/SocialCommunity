<?php
/**
 * @package      Socialcommunity
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Helper;

/**
 * Joomla Helper class.
 *
 * @package      Socialcommunity
 * @subpackage   Helper
 */
abstract class Joomla
{
    /**
     * Return months as options.
     *
     * @param bool $html
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public static function monthsToOptions($html = false)
    {
        $months = array(
            '01' => \JText::_('COM_SOCIALCOMMUNITY_JANUARY'),
            '02' => \JText::_('COM_SOCIALCOMMUNITY_FEBRUARY'),
            '03' => \JText::_('COM_SOCIALCOMMUNITY_MARCH'),
            '04' => \JText::_('COM_SOCIALCOMMUNITY_APRIL'),
            '05' => \JText::_('COM_SOCIALCOMMUNITY_MAY'),
            '06' => \JText::_('COM_SOCIALCOMMUNITY_JUNE'),
            '07' => \JText::_('COM_SOCIALCOMMUNITY_JULY'),
            '08' => \JText::_('COM_SOCIALCOMMUNITY_AUGUST'),
            '09' => \JText::_('COM_SOCIALCOMMUNITY_SEPTEMBER'),
            '10' => \JText::_('COM_SOCIALCOMMUNITY_NOVEMBER'),
            '11' => \JText::_('COM_SOCIALCOMMUNITY_OCTOBER'),
            '12' => \JText::_('COM_SOCIALCOMMUNITY_DECEMBER'),
        );

        $options = array();
        foreach ($months as $key => $value) {
            $options[] = $html ? \JHtml::_('select.option', $key, $value) : ['text' => $key, 'value' => $value];
        }

        return $options;
    }
}
