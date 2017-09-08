<?php
/**
 * @package      Socialcommunity\Socialrofile
 * @subpackage   Token
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Socialprofile\Token;

/**
 * Contract between token cryptography objects.
 *
 * @package      Socialcommunity\Socialrofile
 * @subpackage   Token
 */
interface Cryptograph
{
    public function encrypt(AccessToken $accessToken);
    public function decrypt(AccessToken $accessToken);
}
