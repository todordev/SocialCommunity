<?php
/**
 * @package      SocialCommunity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport("SocialCommunity.init");

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
     * @return    void
     * @since    1.6
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        if ($isNew) {
            if (!JComponentHelper::isEnabled("com_socialcommunity")) {
                return;
            }

            $userId = JArrayHelper::getValue($user, "id", 0, "int");
            $name   = JArrayHelper::getValue($user, "name");
            $this->createProfile($userId, $name);
        }
    }

    /**
     * Remove all sessions for the user name
     *
     * Method is called after user data is deleted from the database
     *
     * @param   array   $user    Holds the user data
     * @param   boolean $success True if user was succesfully stored in the database
     * @param   string  $msg     Message
     *
     * @return  boolean
     *
     * @since   1.6
     */
    public function onUserAfterDelete($user, $success, $msg)
    {
        $userId = JArrayHelper::getValue($user, "id");

        if (!$success or !$userId) {
            return false;
        }

        // Remove profile images.
        $profile = new SocialCommunity\Profile($this->db);
        $profile->load($userId);

        if ($profile->getId()) {

            // Remove profile record.
            $query = $this->db->getQuery(true);
            $query
                ->delete($this->db->quoteName("#__itpsc_profiles"))
                ->where($this->db->quoteName("id") ."=". $userId);

            $this->db->setQuery($query);
            $this->db->execute();

            // Remove profile images.

            /** @var  $params Joomla\Registry\Registry */
            $params       = JComponentHelper::getParams("com_socialcommunity");
            $imagesFolder = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/profiles"));

            // Remove images
            $profile->removeImages($imagesFolder);
        }

        return true;
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array $user    Holds the user data
     * @param   array $options Array holding options (remember, autoregister, group)
     *
     * @return  boolean  True on success
     * @since   1.5
     */
    public function onUserLogin($user, $options)
    {
        if (!JComponentHelper::isEnabled("com_socialcommunity")) {
            return true;
        }

        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, b.id AS profile_id")
            ->from($this->db->quoteName("#__users", "a"))
            ->leftJoin($this->db->quoteName("#__itpsc_profiles", "b") . " ON a.id = b.id")
            ->where("a.username = " . $this->db->quote($user["username"]));

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadAssoc();

        // Create profile
        if (empty($result["profile_id"])) {
            $userId = JArrayHelper::getValue($result, "id");
            $name   = JArrayHelper::getValue($user, "fullname");
            $this->createProfile($userId, $name);
        }

        return true;
    }

    private function createProfile($userId, $name)
    {
        $data = array(
            "id" => (int)$userId,
            "name" => $name,
            "alias" => JApplicationHelper::stringURLSafe($name),
        );

        $profile = new SocialCommunity\Profile($this->db);
        $profile->bind($data);
        $profile->create();
    }
}
