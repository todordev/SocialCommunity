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
 * Value object of contact request.
 *
 * @package      Socialcommunity\Profile\Command
 * @subpackage   Request
 */
class Contact
{
    /**
     * @var int
     */
    protected $location_id;

    /**
     * @var string
     */
    protected $country_code;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $website;

    /**
     * @param int $locationId
     *
     * @return self
     */
    public function setLocationId($locationId)
    {
        $this->location_id = (int)$locationId;

        return $this;
    }

    /**
     * @param string $countryCode
     *
     * @return self
     */
    public function setCountryCode($countryCode)
    {
        $this->country_code = (string)$countryCode;

        return $this;
    }

    /**
     * @param string $website
     *
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocationId()
    {
        return (int)$this->location_id;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return (string)$this->country_code;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return (string)$this->website;
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
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }
}
