<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact;

/**
 * Contract between contact cryptography objects.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 */
interface Cryptograph
{
    public function encrypt(Contact $contact);
    public function decrypt(Contact $contact);
}
