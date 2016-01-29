<?php
/**
 * @package      SocialCommunity
 * @subpackage   Filters
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Filter;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing filters and options.
 *
 * @package      SocialCommunity
 * @subpackage   Filters
 */
class Months
{
    protected $options = array();

    /**
     * Initialize the object.
     *
     * <code>
     * $months    = new Socialcommunity\Filter\Months();
     * </code>
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;

        if (!$this->options) {
            $this->options = array(
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
        }
    }

    /**
     * Return the months as options.
     *
     * <code>
     * $months  = new Socialcommunity\Filter\Months();
     * $options = $months->toOptions();
     * </code>
     *
     * @param bool $html Return options as html tags or associated array used in generic lists.
     *
     * @return array
     */
    public function toOptions($html = false)
    {
        $options = array();

        foreach ($this->options as $key => $value) {
            if (!$html) {
                $options[] = array('text' => $key, 'value' => $value);
            } else {
                $options[] = \JHtml::_('select.option', $key, $value);
            }
        }

        return $options;
    }

    /**
     * Return the months as array.
     *
     * <code>
     * $months  = new Socialcommunity\Filter\Months();
     * $options = $months->toArray();
     * </code>
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }
}
