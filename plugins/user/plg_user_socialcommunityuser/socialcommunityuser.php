<?php
/**
 * @package      SocialCommunity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Socialcommunity.init');

/**
 * This plugin creates or deletes a Social Community profile.
 *
 * @package        SocialCommunity
 * @subpackage     Plugins
 */
class plgUserSocialCommunityUser extends JPlugin
{
    /**
     * Database object
     *
     * @var    JDatabaseDriver
     * @since  3.2
     */
    protected $db;
    
    /**
     * Method is called after user data is stored in the database
     *
     * @param    array   $user    Holds the new user data.
     * @param    boolean $isNew   True if a new user is stored.
     * @param    boolean $success True if user was successfully stored in the database.
     * @param    string  $msg     Message.
     *
     * @throws \InvalidArgumentException
     * @return    void
     * @since    1.6
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        if ($isNew and JComponentHelper::isEnabled('com_socialcommunity')) {
            $userId = Joomla\Utilities\ArrayHelper::getValue($user, 'id', 0, 'int');
            $name   = Joomla\Utilities\ArrayHelper::getValue($user, 'name');
            $this->createProfile($userId, $name);
        }
    }

    /**
     * Remove all sessions for the user name
     *
     * Method is called after user data is deleted from the database
     *
     * @param   array   $user    Holds the user data
     * @param   boolean $success True if user was successfully stored in the database
     * @param   string  $msg     Message
     *
     * @throws \InvalidArgumentException
     * @return  boolean
     *
     * @since   1.6
     */
    public function onUserAfterDelete($user, $success, $msg)
    {
        $userId = Joomla\Utilities\ArrayHelper::getValue($user, 'id', 0, 'int');

        if (!$success or !$userId) {
            return false;
        }

        // Remove profile images.
        $profile = new Socialcommunity\Profile\Profile($this->db);
        $profile->load(array('user_id' => $userId));

        if ($profile->getId()) {
            // Remove profile record.
            $query = $this->db->getQuery(true);
            $query
                ->delete($this->db->quoteName('#__itpsc_profiles'))
                ->where($this->db->quoteName('user_id') .'='. (int)$userId);

            $this->db->setQuery($query);
            $this->db->execute();

            // Remove profile images.

            $params = JComponentHelper::getParams('com_socialcommunity');
            /** @var $params Joomla\Registry\Registry */

            jimport('Prism.libs.init');
            $filesystemHelper = new Prism\Filesystem\Helper($params);
            $mediaFolder = $filesystemHelper->getMediaFolder($userId);
            $filesystem  = $filesystemHelper->getFilesystem();

            $profile->removeImages($filesystem, $mediaFolder);
        }

        return true;
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array $user    Holds the user data
     * @param   array $options Array holding options (remember, autoregister, group)
     *
     * @throws \RuntimeException
     * @return  boolean  True on success
     * @since   1.5
     */
    public function onUserLogin($user, $options)
    {
        if (JComponentHelper::isEnabled('com_socialcommunity')) {
            $query = $this->db->getQuery(true);

            $query
                ->select('a.id, b.user_id')
                ->from($this->db->quoteName('#__users', 'a'))
                ->leftJoin($this->db->quoteName('#__itpsc_profiles', 'b') . ' ON a.id = b.user_id')
                ->where('a.username = ' . $this->db->quote($user['username']));

            $this->db->setQuery($query, 0, 1);
            $result = $this->db->loadAssoc();

            // Create profile
            if ($result !== null and !$result['user_id']) {
                $this->createProfile($result['id'], $user['fullname']);
            }
        }

        return true;
    }

    private function createProfile($userId, $name)
    {
        $data = array(
            'user_id'   => (int)$userId,
            'name'      => $name,
            'alias'     => $name
        );

        $profile = new Socialcommunity\Profile\Profile($this->db);
        $profile->bind($data);
        $profile->store();

        $params = JComponentHelper::getParams('com_socialcommunity');
        /** @var $params Joomla\Registry\Registry */

        $filesystemHelper = new Prism\Filesystem\Helper($params);

        // If the filesystem is local, create a user folder.
        if ($filesystemHelper->isLocal()) {
            $mediaFolder = JPath::clean(JPATH_BASE .'/'. $filesystemHelper->getMediaFolder($userId), '/');
            if (!JFolder::exists($mediaFolder)) {
                JFolder::create($mediaFolder);
            }
        }
    }
}
