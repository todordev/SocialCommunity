<?php
/**
 * @package      Socialcommunity
 * @subpackage   Activities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Activity;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;

/**
 * This is a class that provides functionality for managing activity.
 *
 * @package      Socialcommunity
 * @subpackage   Activities
 */
class Activity implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    public $content;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Set a content of an activity.
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
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set a date when the activity has been created.
     *
     * @param string $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = (string)$created;

        return $this;
    }

    /**
     * Return a date when the activity has been performed.
     *
     * @return string
     */
    public function getCreated()
    {
        return (string)$this->created;
    }

    /**
     * Set a link to an image.
     *
     * @param string $image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = (string)$image;

        return $this;
    }

    /**
     * Return the image which is part of the activity.
     *
     * @return string
     */
    public function getImage()
    {
        return (string)$this->image;
    }

    /**
     * Set a link to a page, which is part of the activity.
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = (string)$url;

        return $this;
    }

    /**
     * Return the URL which is part of the activity.
     *
     * @return string
     */
    public function getUrl()
    {
        return (string)$this->url;
    }

    /**
     * Set a user ID which has performed the activity.
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }

    /**
     * Return an ID of an user which has performed the activity.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }
}
