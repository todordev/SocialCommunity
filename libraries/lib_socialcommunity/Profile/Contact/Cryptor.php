<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Joomla\Registry\Registry;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

/**
 * This class provides business logic for encrypting and decrypting profile contacts.
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
     * Encrypt contact data.
     *
     * @param Contact $contact
     *
     * @return Contact
     *
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     * @throws BadFormatException
     */
    public function encrypt(Contact $contact)
    {
        $key        = KeyProtectedByPassword::loadFromAsciiSafeString($this->secret_key);
        $secretKey  = $key->unlockKey($this->password);

        $contact_ = clone $contact;
        $contact_->setPhone(Crypto::encrypt($contact->getPhone(), $secretKey, true));
        $contact_->setAddress(Crypto::encrypt($contact->getAddress(), $secretKey, true));

        return $contact_;
    }

    /**
     * Decrypt contact data.
     *
     * @param Contact $contact
     *
     * @return Contact
     *
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     * @throws BadFormatException
     */
    public function decrypt(Contact $contact)
    {
        $key        = KeyProtectedByPassword::loadFromAsciiSafeString($this->secret_key);
        $secretKey  = $key->unlockKey($this->password);

        $contact_ = clone $contact;
        $contact_->setPhone(Crypto::decrypt($contact->getPhone(), $secretKey, true));
        $contact_->setAddress(Crypto::decrypt($contact->getAddress(), $secretKey, true));

        return $contact_;
    }
}
