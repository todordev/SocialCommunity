<?php
/**
 * @package      Socialcommunity
 * @subpackage   Socialprofile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Socialcommunity\Socialprofile\Token\AccessToken;

/**
 * This class provides business logic for managing a social profile.
 *
 * @package      Socialcommunity
 * @subpackage   Socialprofile
 */
class Socialprofile implements Entity
{
    use EntityId;

    protected $link;
    protected $image_square;
    protected $service_user_id;
    protected $service;
    protected $user_id;
    protected $secret_key;

    /**
     * @var AccessToken
     */
    protected $access_token;

    public function bind(array $data, array $ignored = array())
    {
        $properties = get_object_vars($this);

        if (array_key_exists('access_token', $data) and !in_array('access_token', $ignored, true)) {
            if ($data['access_token'] instanceof AccessToken) {
                $this->access_token = $data['access_token'];
            } else {
                $this->access_token = new AccessToken($data['access_token'], $data['expires_at']);
            }
            unset($data['access_token'], $data['expires_at']);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties) and !in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return the access token.
     *
     * @return AccessToken|null
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Set the access token.
     *
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->access_token = $accessToken;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return (string)$this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Return the user ID of the social profile on its service.
     *
     * @return int
     */
    public function getServiceUserId()
    {
        return $this->service_user_id;
    }

    /**
     * @param int $userId
     */
    public function setServiceUserId($userId)
    {
        $this->service_user_id = $userId;
    }

    /**
     * Return the service name of the social profile.
     *
     * @return string
     */
    public function getService()
    {
        return (string)$this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secret_key = (string)$secretKey;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return (string)$this->secret_key;
    }

    /**
     * @param string $imageSquare
     */
    public function setImageSquare($imageSquare)
    {
        $this->image_square = (string)$imageSquare;
    }

    /**
     * @return string
     */
    public function getImageSquare()
    {
        return (string)$this->image_square;
    }
}
