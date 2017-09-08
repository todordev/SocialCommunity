<?php
/**
 * @package      Socialcommunity\Profile\Command
 * @subpackage   Request
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Request;

/**
 * Value object of basic profile request.
 *
 * @package      Socialcommunity\Profile\Command
 * @subpackage   Request
 */
class Basic
{
    /**
     * @var string
     */
    protected $name;
    protected $bio;
    protected $birthday;
    protected $gender;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    /**
     * @return string
     */
    public function getBio()
    {
        return (string)$this->bio;
    }

    /**
     * @param string $bio
     * @return self
     */
    public function setBio($bio)
    {
        $this->bio = (string)$bio;

        return $this;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return (string)$this->birthday;
    }

    /**
     * @param string $birthday
     * @return self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = (string)$birthday;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return (string)$this->gender;
    }

    /**
     * @param string $gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = (string)$gender;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $userId
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }
}
