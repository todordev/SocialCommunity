<?php
/**
 * @package      SocialCommunity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use \Prism\Database\Condition\Condition;
use \Prism\Database\Condition\Conditions;
use \Prism\Database\Request\Request;
use \Prism\Database\Request\Field;
use \Prism\Database\Request\Fields;
use \Socialcommunity\Profile\Mapper;
use \Socialcommunity\Profile\Repository;
use \Socialcommunity\Profile\Gateway\JoomlaGateway;

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
class plgUserSocialcommunityUser extends JPlugin
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
            $userId = ArrayHelper::getValue($user, 'id', 0, 'int');
            $name   = ArrayHelper::getValue($user, 'name');
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

        $conditions = new  Conditions;
        $conditions->addCondition(new Condition(['column' => 'user_id', 'value' => $userId]));

        $fields = new Fields;
        $fields
            ->addField(new Field(['column' => 'id']))
            ->addField(new Field(['column' => 'user_id']))
            ->addField(new Field(['column' => 'image']))
            ->addField(new Field(['column' => 'image_icon']))
            ->addField(new Field(['column' => 'image_square']))
            ->addField(new Field(['column' => 'image_small']));

        $databaseRequest = new Request();
        $databaseRequest
            ->setConditions($conditions)
            ->setFields($fields);

        // Remove profile images.
        $mapper     = new Mapper(new JoomlaGateway(JFactory::getDbo()));
        $repository = new Repository($mapper);
        $profile    = $repository->fetch($databaseRequest);

        if ($profile->getId()) {
            // ### Remove profile images.

            $params = JComponentHelper::getParams('com_socialcommunity');
            /** @var $params Joomla\Registry\Registry */

            jimport('Prism.vendor.init');
            $filesystemHelper   = new Prism\Filesystem\Helper($params);
            $mediaFolder        = $filesystemHelper->getMediaFolder($userId);
            $filesystem         = $filesystemHelper->getFilesystem();

            $profileImage               = new \Socialcommunity\Value\Profile\Image();
            $profileImage->image        = $profile->getImage();
            $profileImage->image_icon   = $profile->getImageIcon();
            $profileImage->image_small  = $profile->getImageSmall();
            $profileImage->image_square = $profile->getImageSquare();
            $profileImage->profile_id   = $profile->getId();

            $deleteImagesCommand = new \Socialcommunity\Profile\Command\DeleteImage($profileImage, $filesystem, $mediaFolder);
            $deleteImagesCommand->handle();

            // Remove user profile.
            $removeProfileCommand = new \Socialcommunity\Profile\Command\RemoveProfile($profile);
            $removeProfileCommand->setGateway(new \Socialcommunity\Profile\Command\Gateway\Joomla\RemoveProfile(JFactory::getDbo()));
            $removeProfileCommand->handle();
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
        $alias = Prism\Utilities\StringHelper::stringUrlSafe($name);

        $createProfileCommand  = new \Socialcommunity\Profile\Command\CreateProfile($userId, $name, $alias);
        $createProfileCommand->setGateway(new \Socialcommunity\Profile\Command\Gateway\Joomla\CreateProfile(JFactory::getDbo()));
        $createProfileCommand->handle();

        $params = JComponentHelper::getParams('com_socialcommunity');
        /** @var $params Joomla\Registry\Registry */

        $filesystemHelper = new Prism\Filesystem\Helper($params);

        // If the filesystem is local, create a user folder.
        if ($filesystemHelper->isLocal()) {
            $mediaFolder = JPath::clean(JPATH_ROOT .'/'. $filesystemHelper->getMediaFolder($userId), '/');
            if (!JFolder::exists($mediaFolder)) {
                JFolder::create($mediaFolder);
            }
        }
    }
}
