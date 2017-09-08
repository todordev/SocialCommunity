<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Token;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Joomla\Registry\Registry;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

/**
 * This class provides business logic for encrypting and decrypting social profile access token.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 */
class Cryptor implements Cryptograph
{
    protected $secret_key;
    protected $password;

    /**
     * @var Registry
     */
    protected $encryptor;

    public function __construct($secretKey, $password)
    {
        $this->secret_key = $secretKey;
        $this->password   = $password;
    }

    /**
     * Encrypt access token data.
     *
     * @param AccessToken $accessToken
     *
     * @return AccessToken
     *
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     * @throws BadFormatException
     */
    public function encrypt(AccessToken $accessToken)
    {
        $key        = KeyProtectedByPassword::loadFromAsciiSafeString($this->secret_key);
        $secretKey  = $key->unlockKey($this->password);

        $token      = Crypto::encrypt($accessToken->getValue(), $secretKey, true);
        $expiresAt  = $accessToken->getExpiresAt() ? $accessToken->getExpiresAt()->format('Y-m-d H:i:s') : '';

        return new AccessToken($token, $expiresAt);
    }

    /**
     * Decrypt access token data.
     *
     * @param AccessToken $accessToken
     *
     * @return AccessToken
     *
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     * @throws BadFormatException
     */
    public function decrypt(AccessToken $accessToken)
    {
        $key        = KeyProtectedByPassword::loadFromAsciiSafeString($this->secret_key);
        $secretKey  = $key->unlockKey($this->password);

        $token      = Crypto::decrypt($accessToken->getValue(), $secretKey, true);
        $expiresAt  = $accessToken->getExpiresAt() ? $accessToken->getExpiresAt()->format('Y-m-d H:i:s') : '';

        return new AccessToken($token, $expiresAt);
    }
}
