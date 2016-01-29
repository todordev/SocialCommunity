<?php
/**
 * @package      SocialCommunity
 * @subpackage   Profiles
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Socialcommunity\Wall\User;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This is a class that provides functionality for managing user post.
 *
 * @package      SocialCommunity
 * @subpackage   Wall
 */
class Post extends Prism\Database\Table
{
    protected $id = 0;
    protected $content;
    protected $url;
    protected $media;
    protected $created;
    protected $user_id = 0;
    
    /**
     * Load the data of the object from database.
     *
     * <code>
     * $postId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.content, a.media, a.url, a.created, a.user_id')
            ->from($this->db->quoteName('#__itpsc_wall', 'a'));

        // Filter by keys.
        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) . ' = ' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Store data to database.
     *
     * <code>
     * $data = array(
     *    "user_id" => 1,
     *    "content" => "The adventure just has begun!"
     * );
     *
     * $entity    = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->bind($data);
     * $entity->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $content         = (!$this->content) ? 'NULL' : $this->db->quote($this->content);
        $url             = (!$this->url) ? 'NULL' : $this->db->quote($this->url);
        $media           = (!$this->media) ? 'NULL' : $this->db->quote($this->media);
        $created         = (!$this->created) ? 'NULL' : $this->db->quote($this->created);

        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__itpsc_userwalls'))
            ->set($this->db->quoteName('content') . '=' . $content)
            ->set($this->db->quoteName('url') . '=' . $url)
            ->set($this->db->quoteName('media') . '=' . $media)
            ->set($this->db->quoteName('created') . '=' . $created)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $content    = (!$this->content) ? 'NULL' : $this->db->quote($this->content);
        $url        = (!$this->url) ? 'NULL' : $this->db->quote($this->url);
        $media      = (!$this->media) ? 'NULL' : $this->db->quote($this->media);

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__itpsc_userwalls'))
            ->set($this->db->quoteName('content') . '=' . $content)
            ->set($this->db->quoteName('url') . '=' . $url)
            ->set($this->db->quoteName('media') . '=' . $media)
            ->where($this->db->quoteName('id') .'='. (int)$this->id)
            ->where($this->db->quoteName('user_id') .'='. (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
    
    /**
     * Get record ID.
     *
     * <code>
     * $userId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load(array('user_id' => $userId));
     *
     * if (!$entity->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set the ID of the post.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setUserId(1);
     * </code>
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }
    
    /**
     * Get user ID.
     *
     * <code>
     * $postId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     *
     * echo $entity->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set the ID of the user.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setUserId(1);
     * </code>
     *
     * @param int $id
     *
     * @return self
     */
    public function setUserId($id)
    {
        $this->user_id = (int)$id;

        return $this;
    }

    /**
     * Return the content of the post.
     *
     * <code>
     * $userId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load(array('user_id' => $userId));
     *
     * echo $entity->getContent();
     * </code>
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the post.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setContent('My adventure...');
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
     * Return the URL to the media.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     *
     * $url = $entity->getUrl();
     * </code>
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL of the media.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setUrl('http://domain.com/picture.png');
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
     * Return media type.
     *
     * <code>
     * $postId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     *
     * $media = $entity->getMedia();
     * </code>
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Return the date when the record has been created.
     *
     * <code>
     * $postId = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     *
     * echo $entity->getCreated();
     * </code>
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set date and time when the record has been created.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setCreated('2016-01-01 12:00:01');
     * </code>
     *
     * @param string $date
     *
     * @return self
     */
    public function setCreated($date)
    {
        $this->created = $date;

        return $this;
    }

    /**
     * Set the type of the media - website, picture, video.
     *
     * <code>
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->setMedia('website');
     * </code>
     *
     * @param string $media
     *
     * @return self
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Remove the entity from the wall.
     *
     * <code>
     * $postId   = 1;
     *
     * $entity   = new Socialcommunity\Wall\User\Post(\JFactory::getDbo());
     * $entity->load($postId);
     *
     * $entity->remove();
     * </code>
     */
    public function remove()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_userwalls'))
            ->where($this->db->quoteName('id') .'='. (int)$this->id)
            ->where($this->db->quoteName('user_id') .'='. (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();
    }
}
