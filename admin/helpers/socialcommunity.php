<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class SocialCommunityHelper
{
    static protected $extension = 'com_socialcommunity';

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName === 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_PROFILES'),
            'index.php?option=' . self::$extension . '&view=profiles',
            $vName === 'profiles'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_LOCATIONS'),
            'index.php?option=' . self::$extension . '&view=locations',
            $vName === 'locations'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_COUNTRIES'),
            'index.php?option=' . self::$extension . '&view=countries',
            $vName === 'countries'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_SOCIALCOMMUNITY_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=socialcommunity',
            $vName === 'plugins'
        );
    }

    public static function prepareBirthday($data)
    {
        $birthdayDay   = Joomla\Utilities\ArrayHelper::getValue($data['birthday'], 'day', 0, 'int');
        $birthdayMonth = Joomla\Utilities\ArrayHelper::getValue($data['birthday'], 'month', 0, 'int');
        $birthdayYear  = Joomla\Utilities\ArrayHelper::getValue($data['birthday'], 'year', 0, 'int');
        if (!$birthdayDay) {
            $birthdayDay = '00';
        }
        if (!$birthdayMonth) {
            $birthdayMonth = '00';
        }
        if (!$birthdayYear) {
            $birthdayYear = '0000';
        }

        $birthday = $birthdayYear . '-' . $birthdayMonth . '-' . $birthdayDay;

        $date = new Prism\Validator\Date($birthday);
        if (!$date->isValid()) {
            $birthday = '0000-00-00';
        }

        return $birthday;
    }

    /**
     * This method updates the name of user in
     * the Joomla! users table.
     *
     * @param int $id
     * @param string $name
     */
    public static function updateName($id, $name)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName('#__users'))
            ->set($db->quoteName('name') . '=' . $db->quote($name))
            ->where($db->quoteName('id') . '=' . (int)$id);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Create profiles for orphan users.
     */
    public static function createProfiles()
    {
        $db    = JFactory::getDbo();
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

                $data = array(
                    'user_id' => $result['id'],
                    'name' => $result['name'],
                    'alias' => $result['name'],
                    'active' => Prism\Constants::ACTIVE
                );

                $profile->bind($data);
                $profile->store();
            }
        }
    }
}
