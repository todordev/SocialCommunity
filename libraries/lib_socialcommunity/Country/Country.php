<?php
/**
 * @package      Socialcommunity
 * @subpackage   Countries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Country;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;

/**
 * This class contains methods that are used for managing location.
 *
 * @package      Socialcommunity
 * @subpackage   Countries
 */
class Country implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    protected $name;
    protected $latitude;
    protected $longitude;
    protected $code;
    protected $locale;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return the name of the country.
     *
     * @param bool $includeCountryCode A flag that indicate to be included country code to the name.
     *
     * @return string
     */
    public function getName($includeCountryCode = false)
    {
        if ($includeCountryCode and ($this->code !== null and $this->code !== '')) {
            return $this->name .', '. $this->code;
        }

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
    public function setCode($countryCode)
    {
        $this->code = (string)$countryCode;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = (string)$timezone;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return (string)$this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = (string)$locale;
    }
}
