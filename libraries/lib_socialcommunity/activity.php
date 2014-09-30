<?php
/**
 * @package      SocialCommunity
 * @subpackage   Activities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing activity.
 *
 * @package      SocialCommunity
 * @subpackage   Activities
 */
class SocialCommunityActivity
{
    public $id;
    public $content;
    public $image;
    public $url;
    public $created;
    public $user_id;

    /**
     * Driver of the database.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Load activity record.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->load($activityId);
     * </code>
     *
     * @param integer $id
     */
    public function load($id)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.content, a.image, a.url, a.created, a.user_id")
            ->from($this->db->quoteName("#__itpsc_activities", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }

    }

    /**
     * Set data about activity to object parameters.
     *
     * <code>
     * $data = array(
     *  "content"  => "A have been registered.",
     *  "user_id" => 1
     * );
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->bind($data);
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
     * Store the data about activity to database.
     *
     * <code>
     * $data = array(
     *  "content"  => "A have been registered.",
     *  "user_id" => 1
     * );
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->bind($data);
     * $activity->store();
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
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__itpsc_activities"))
            ->set($this->db->quoteName("content") . "=" . $this->db->quote($this->content))
            ->set($this->db->quoteName("image") . "=" . $this->db->quote($this->image))
            ->set($this->db->quoteName("url") . "=" . $this->db->quote($this->url))
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        $query = $this->db->getQuery(true);

        if (!$this->created) {
            $date          = new JDate();
            $this->created = $date->toSql();
        }

        $query
            ->insert($this->db->quoteName("#__itpsc_activities"))
            ->set($this->db->quoteName("content") . "=" . $this->db->quote($this->content))
            ->set($this->db->quoteName("created") . "=" . $this->db->quote($this->created))
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id);

        if (!empty($this->image)) {
            $query->set($this->db->quoteName("image") . "=" . $this->db->quote($this->image));
        }

        if (!empty($this->url)) {
            $query->set($this->db->quoteName("url") . "=" . $this->db->quote($this->url));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    /**
     * Set a content of an activity.
     *
     * <code>
     * $content = "...";
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->setContent($content);
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
     * Return an activity content.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->load($activityId);
     *
     * $content = $activity->getContent();
     * </code>
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set a date when the activity has been created.
     *
     * <code>
     * $created = "2014-02-02";
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->setContent($created);
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
     * Return a date when the activity has been performed.
     *
     * <code>
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $date = $activity->getCreated();
     * </code>
     *
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set a link to an image.
     *
     * <code>
     * $image = "...";
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->setImage($image);
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
     * Return the image which is part of the activity.
     *
     * <code>
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $image = $activity->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set a link to a page, which is part of the activity.
     *
     * <code>
     * $url = "...";
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->setUrl($url);
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
     * Return the URL which is part of the activity.
     *
     * <code>
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $url = $activity->getUrl();
     * </code>
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set a user ID which has performed the activity.
     *
     * <code>
     * $userId = 1;
     *
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $activity->setUrl($userId);
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
     * Return an ID of an user which has performed the activity.
     *
     * <code>
     * $activity   = new SocialCommunityActivity(JFactory::getDbo());
     * $userId     = $activity->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
