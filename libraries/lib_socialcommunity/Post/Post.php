<?php
/**
 * @package      Socialcommunity
 * @subpackage   Post
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\Populator;

/**
 * This is a class that provides functionality for managing user post.
 *
 * @package      Socialcommunity
 * @subpackage   Post
 */
class Post implements Entity
{
    use EntityId, Populator;

    protected $content;
    protected $url;
    protected $media;
    protected $created_at;
    protected $user_id = 0;

    /**
     * Get user ID.
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
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the post.
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL of the media.
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
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Return the date when the record has been created.
     *
     * @return string
     */
    public function createdAt()
    {
        return $this->created_at;
    }

    /**
     * Set date and time when the record has been created.
     *
     * @param string $date
     *
     * @return self
     */
    public function setCreatedAt($date)
    {
        $this->created_at = $date;

        return $this;
    }

    /**
     * Set the type of the media - website, picture, video.
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
}
