<?php
/**
 * @package      SocialCommunity
 * @subpackage   Notifications
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace SocialCommunity;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing notification.
 *
 * @package      SocialCommunity
 * @subpackage   Notifications
 */
class Notification extends Prism\Database\Table
{
    public $id;
    public $content;
    public $status = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Load notification record from database.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($notificationId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.content, a.image, a.url, a.created, a.status, a.user_id")
            ->from($this->db->quoteName("#__itpsc_notifications", "a"));

        // Filter by keys.
        if (!is_array($keys)) {
            $query->where("a.id = " . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName($key) . " = " . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * Reset the properties of the object.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($notificationId);
     *
     * if (...) {
     *    $notification->reset();
     * }
     * </code>
     */
    public function reset()
    {
        $parameters = get_object_vars($this);

        foreach ($parameters as $key => $value) {
            if (strcmp("db", $key) == 0) {
                continue;
            }
            $this->$key = null;
        }

        $this->status = 0;
    }

    /**
     * Store the data about notification to database.
     *
     * <code>
     * $data = array(
     *  "content"  => "A have been registered.",
     *  "user_id" => 1
     * );
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->bind($data);
     * 
     * $notification->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) {
            $this->insertObject();
        } else {
            $this->updateObject();
        }
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__itpsc_notifications"))
            ->set($this->db->quoteName("content") . "=" . $this->db->quote($this->content))
            ->set($this->db->quoteName("image") . "=" . $this->db->quote($this->image))
            ->set($this->db->quoteName("url") . "=" . $this->db->quote($this->url))
            ->set($this->db->quoteName("status") . "=" . (int)$this->status)
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        if (!$this->created) {
            $date          = new \JDate();
            $this->created = $date->toSql();
        }

        $query
            ->insert($this->db->quoteName("#__itpsc_notifications"))
            ->set($this->db->quoteName("content") . " = " . $this->db->quote($this->content))
            ->set($this->db->quoteName("created") . " = " . $this->db->quote($this->created))
            ->set($this->db->quoteName("status") . " = " . (int)$this->status)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id);

        if (!empty($this->image)) {
            $query->set($this->db->quoteName("image") . " = " . $this->db->quote($this->image));
        }

        if (!empty($this->url)) {
            $query->set($this->db->quoteName("url") . " = " . $this->db->quote($this->url));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    /**
     * Remove a notification from database and reset current object.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($notificationId);
     *
     * if ($notification->getId()) {
     *     $notification->remove();
     * }
     * </code>
     */
    public function remove()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__itpsc_notifications"))
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();
    }

    /**
     * Get notification ID.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * if (!$notification->getId()) {
     * ....
     * )
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set user ID.
     * 
     * <code>
     * $notificationId = 1;
     * $userId = 2;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setUser($userId);
     * </code>
     * 
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Set the content of current notification.
     *
     * <code>
     * $notificationId = 1;
     * $content = 2;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setUser($userId);
     * </code>
     *
     * @param string $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Check the status of notification.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     *
     * if ($notification->isArchived()) {
     * ....
     * }
     * </code>
     *
     * @return bool
     */
    public function isArchived()
    {
        return (!$this->status) ? false : true;
    }

    /**
     * Set the image of a notification.
     *
     * <code>
     * $notificationId = 1;
     * $image = "....";
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setImage($image);
     * </code>
     *
     * @param string $image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set a URL where the notification will point.
     *
     * <code>
     * $notificationId = 1;
     * $url = "....";
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setUrl($url);
     * </code>
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set a date when the notification has been created.
     *
     * <code>
     * $notificationId = 1;
     * $date = "12-13-2015";
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setCreated($date);
     * </code>
     *
     * @param string $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set the status of the notification.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->setStatus(SocialCommunityConstants::ARCHIVED);
     * </code>
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = (int)$status;

        return $this;
    }

    /**
     * Check if the notification is read.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * if (!$notification->isRead()) {
     * ....
     * )
     * </code>
     *
     * @return bool
     */
    public function isRead()
    {
        return (bool)$this->status;
    }

    /**
     * Update the status of the notification.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new SocialCommunity\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $notification->updateStatus(Prism\Constants::READ);
     * </code>
     *
     * @param int $status Status of a notification (0 - Not Read, 1 - Read, -2 - trashed )
     *
     * @throw InvalidArgumentException
     */
    public function updateStatus($status)
    {
        if (!$this->id or !$this->user_id) {
            throw new \InvalidArgumentException(\JText::_("LIB_SOCIALCOMMUNITY_ERROR_INVALID_PARAMETER_ID_OR_USER_ID"));
        }

        $this->status = (int)$status;

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__itpsc_notifications"))
            ->set($this->db->quoteName("status") . "=" . (int)$this->status)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id)
            ->where($this->db->quoteName("user_id") . "=" . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
