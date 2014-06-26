<?php
/**
 * @package      SocialCommunity
 * @subpackage   Notifications
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing notification.
 *
 * @package      SocialCommunity
 * @subpackage   Notifications
 */
class SocialCommunityNotification
{
    public $id;
    public $content;
    public $status = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Set database object.
     *
     * <code>
     * $notification   = new SocialCommunityNotification();
     * $notification->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;
        return $this;
    }
    
    /**
     * Load notification record from database.
     *
     * <code>
     * $notificationId = 1;
     *
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
     * $notification->load($notificationId);
     * </code>
     *
     * @param integer $id
     */
    public function load($id)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.content, a.image, a.url, a.created, a.status, a.user_id")
            ->from($this->db->quoteName("#__itpsc_notifications", "a"))
            ->where("a.id = " . (int)$id);

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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
     * $notification->load($notificationId);
     *
     * if (...) {
     *    $notification->reset();
     * }
     * </code>
     *
     */
    public function reset()
    {
        $parameters = get_object_vars($this);

        foreach ($parameters as $key) {

            if (strcmp("db", $key)) {
                continue;
            }

            $this->$key = null;
        }

        $this->status = 0;
    }

    /**
     * Set data about notification to object parameters.
     *
     * <code>
     * $data = array(
     *  "content"  => "You have won a reward...",
     *  "user_id" => 1
     * );
     *
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
     * $notification->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
     * $notification->bind($data);
     * $notification->store();
     * </code>
     */
    protected function store()
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
            $date          = new JDate();
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
     * $notification   = new SocialCommunityNotification();
     * $notification->setDb(JFactory::getDbo());
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
     * Set user ID.
     * 
     * <code>
     * $notificationId = 1;
     * $userId = 2;
     *
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
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
     * $notification   = new SocialCommunityNotification();
     * $notification->setDb(JFactory::getDbo());
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
     * $date = "12-13-2014";
     *
     * $notification   = new SocialCommunityNotification();
     * $notification->setDb(JFactory::getDbo());
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
     * $notification   = new SocialCommunityNotification(JFactory::getDbo());
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
}
