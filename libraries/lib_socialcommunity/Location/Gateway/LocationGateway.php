<?php
/**
 * @package         Socialcommunity\Location
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Location\Gateway;

use Prism\Domain\RichFetcher;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Location
 * @subpackage      Gateway
 */
interface LocationGateway extends RichFetcher
{

}
