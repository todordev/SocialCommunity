<?php
/**
 * @package      Socialcommunity
 * @subpackage   Notifications
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Notification;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;

/**
 * This is a class that provides functionality for managing notification.
 *
 * @package      Socialcommunity
 * @subpackage   Notifications
 */
class Notification implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    public $content;
    public $status = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int)$this->status;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Reset the properties of the object.
     */
    public function reset()
    {
        parent::reset();
        $this->status = 0;
    }

    /**
     * Remove a notification from database and reset current object.
     */
    public function remove()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__itpsc_notifications'))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();
    }

    /**
     * Set user ID.
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
     * Set the content of current notification.
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
     * @return bool
     */
    public function isArchived()
    {
        return (!$this->status) ? false : true;
    }

    /**
     * Set the image of a notification.
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
     * @return bool
     */
    public function isRead()
    {
        return (bool)$this->status;
    }
}
