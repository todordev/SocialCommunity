<?php
/**
 * @package      SocialCommunity
 * @subpackage   Activities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace SocialCommunity;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing activity.
 *
 * @package      SocialCommunity
 * @subpackage   Activities
 */
class Activity extends Prism\Database\Table
{
    public $id;
    public $content;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Load activity record.
     *
     * <code>
     * $keys = array(
     *    'id' => 1,
     *    'user_id' => 2
     * );
     *
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
     * $activity->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param $options $keys
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.content, a.image, a.url, a.created, a.user_id")
            ->from($this->db->quoteName("#__itpsc_activities", "a"));

        // Filter by keys.
        if (!is_array($keys)) {
            $query->where("a.id = " . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName($key) . " = " . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);

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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
            $date          = new \JDate();
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $created = "2015-02-02";
     *
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
     * $activity   = new SocialCommunity\Activity(\JFactory::getDbo());
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
