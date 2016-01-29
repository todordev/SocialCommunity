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
class Countries
{
    /**
     * Database driver.
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    protected $data;

    /**
     * Initialize the object.
     *
     * <code>
     * $months    = new Socialcommunity\Filter\Months();
     * </code>
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    protected function load()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from($this->db->quoteName('#__itpsc_countries', 'a'))
            ->order('a.name ASC');

        // Get the options.
        $this->db->setQuery($query);
        $this->data = $this->db->loadAssocList('id', 'name');
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
        // Load data from database.
        if ($this->data === null) {
            $this->load();
        }

        $options = array();

        foreach ($this->data as $value => $text) {
            if (!$html) {
                $options[] = array('value' => $value, 'text' => $text);
            } else {
                $options[] = \JHtml::_('select.option', $value, $text);
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
        // Load data from database.
        if ($this->data === null) {
            $this->load();
        }

        return $this->data;
    }
}
