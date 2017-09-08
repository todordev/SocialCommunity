<?php
/**
 * @package      Socialcommunity
 * @subpackage   Locations
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Location;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;

/**
 * This class contains methods that are used for managing location.
 *
 * @package      Socialcommunity
 * @subpackage   Locations
 */
class Location implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    protected $name;
    protected $latitude;
    protected $longitude;
    protected $country_code;
    protected $state_code;
    protected $timezone;

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Return a country code.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Return the name of the location.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = (string)$latitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = (string)$longitude;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->country_code = (string)$countryCode;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = (string)$timezone;
    }
}
