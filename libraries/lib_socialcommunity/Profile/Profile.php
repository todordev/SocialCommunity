<?php
/**
 * @package      Socialcommunity
 * @subpackage   Profile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

use Joomla\Registry\Registry;
use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;

/**
 * This class provides business logic for managing a profile.
 *
 * @package      Socialcommunity
 * @subpackage   Profile
 */
class Profile implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    protected $name;
    protected $alias;
    protected $image;
    protected $image_icon;
    protected $image_square;
    protected $image_small;
    protected $bio;
    protected $gender;
    protected $user_id;
    protected $location_id;
    protected $country_code;
    protected $website;
    protected $slug;
    protected $active = 0;

    /**
     * @var Registry
     */
    protected $params;

    /**
     * @var Birthday
     */
    protected $birthday;

    public function __construct()
    {
        $this->birthday = new Birthday();
    }

    public function bind(array $data, array $ignored = array())
    {
        $properties = get_object_vars($this);

        // Parse parameters of the object if they exists.
        if (array_key_exists('params', $data) and !in_array('params', $ignored, true)) {
            if ($data['params'] instanceof Registry) {
                $this->params = $data['params'];
            } else {
                $this->params = new Registry($data['params']);
            }
            unset($data['params']);
        }

        if (array_key_exists('birthday', $data) and !in_array('birthday', $ignored, true)) {
            if ($data['birthday'] instanceof Birthday) {
                $this->birthday = $data['birthday'];
            } else {
                $this->birthday = new Birthday(new \DateTime($data['birthday']));
            }
            unset($data['birthday']);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties) and !in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return the name of the holder.
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * Return bio of this profile.
     *
     * @return string
     */
    public function getBio()
    {
        return (string)$this->bio;
    }

    /**
     * Return the status of the profile.
     *
     * @return int
     */
    public function getStatus()
    {
        return (int)$this->active;
    }

    /**
     * Return the user ID of the profile.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return the location ID of the profile.
     *
     * @return int
     */
    public function getLocationId()
    {
        return (int)$this->location_id;
    }

    /**
     * Return the birthday of the user.
     *
     * @return Birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * set the birthday of the user.
     *
     * @param \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = new Birthday($birthday);
    }

    /**
     * Return the gender of the profile.
     *
     * @return string
     */
    public function getGender()
    {
        return (string)$this->gender;
    }

    /**
     * Check if profile is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->active;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return (string)$this->alias;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return (string)$this->image;
    }

    /**
     * @return string
     */
    public function getImageIcon()
    {
        return (string)$this->image_icon;
    }

    /**
     * @return string
     */
    public function getImageSquare()
    {
        return (string)$this->image_square;
    }

    /**
     * @return string
     */
    public function getImageSmall()
    {
        return (string)$this->image_small;
    }

    /**
     * @return Registry
     */
    public function getParams()
    {
        return $this->params;
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
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = (string)$image;
    }

    /**
     * @param string $imageIcon
     */
    public function setImageIcon($imageIcon)
    {
        $this->image_icon = (string)$imageIcon;
    }

    /**
     * @param string $imageSquare
     */
    public function setImageSquare($imageSquare)
    {
        $this->image_square = (string)$imageSquare;
    }

    /**
     * @param string $imageSmall
     */
    public function setImageSmall($imageSmall)
    {
        $this->image_small = (string)$imageSmall;
    }

    /**
     * Return profile slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
