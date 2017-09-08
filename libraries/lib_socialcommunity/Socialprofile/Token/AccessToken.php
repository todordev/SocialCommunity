<?php
/**
 * @package      Socialcommunity\Socialprofile
 * @subpackage   Token
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Token;

/**
 * Value object of the access token.
 *
 * @package      Socialcommunity\Socialprofile
 * @subpackage   Token
 */
class AccessToken
{
    /**
     * The access token value.
     *
     * @var string
     */
    protected $value = '';

    /**
     * Date when token expires.
     *
     * @var \DateTime|null
     */
    protected $expires_at;

    /**
     * Create a new access token entity.
     *
     * @param string $accessToken
     * @param string $expiresAt
     */
    public function __construct($accessToken, $expiresAt = '')
    {
        $this->value = $accessToken;
        if ($expiresAt) {
            $this->setExpiresAtFromString($expiresAt);
        }
    }

    /**
     * Getter for expiresAt.
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Checks the expiration of the access token.
     *
     * @return boolean|null
     */
    public function isExpired()
    {
        if ($this->getExpiresAt()) {
            return $this->getExpiresAt()->getTimestamp() < time();
        }

        return null;
    }

    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Setter for expires_at.
     *
     * @param string $datetime
     */
    protected function setExpiresAtFromString($datetime)
    {
        $this->expires_at = new \DateTime($datetime);
    }
}
