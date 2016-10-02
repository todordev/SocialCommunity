<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Get a list of items
 *
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityModelProfiles extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'user_id', 'a.user_id',
                'registerDate', 'b.registerDate',
            );
        }

        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        // Load the filter state.
        $value = (string)$this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.alias, a.image_icon, a.user_id, ' .
                'b.name, b.registerDate, ' .
                'c.name as country'
            )
        );
        $query->from($db->quoteName('#__itpsc_profiles', 'a'));
        $query->leftJoin($db->quoteName('#__users', 'b') . ' ON a.user_id = b.id');
        $query->leftJoin($db->quoteName('#__itpsc_countries', 'c') . ' ON a.country_id = c.id');

        // Filter by search in title
        $search = $this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.user_id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('a.name LIKE ' . $quoted);
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }

    /**
     * This method updates the name of user in
     * the Joomla! users table.
     */
    public function createProfiles()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from($db->quoteName('#__users', 'a'))
            ->leftJoin($db->quoteName('#__itpsc_profiles', 'b') . ' ON a.id = b.user_id')
            ->where('b.user_id IS NULL');

        $db->setQuery($query);

        $results = $db->loadAssocList();

        if ($results !== null and count($results) > 0) {

            foreach ($results as $result) {
                $profile = new Socialcommunity\Profile\Profile($db);

                $profile->setUserId($result['id']);
                $profile->setName($result['name']);
                $profile->setAlias($result['name']);

                $profile->store();
            }
        }

    }
}
