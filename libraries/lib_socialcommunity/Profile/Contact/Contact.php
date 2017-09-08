<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;

/**
 * This class provides business logic of profile contact.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 */
class Contact implements Entity
{
    use EntityId;

    protected $phone;
    protected $address;
    protected $user_id;
    protected $secret_key;

    /**
     * Set phone number.
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
    }

    /**
     * Set address.
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = (string)$address;
    }

    /**
     * Return phone number.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Return address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set user ID.
     *
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;
    }

    /**
     * Return user ID.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set a secret key.
     *
     * @param string $key
     */
    public function setSecretKey($key)
    {
        $this->secret_key = (string)$key;
    }

    /**
     * Return the secret key.
     *
     * @return int
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }
}
