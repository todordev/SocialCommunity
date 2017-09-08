<?php
/**
 * @package         Socialcommunity\Country\Data
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Country\Data\Gateway;

use Prism\Database\Request\Request;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Socialcommunity\Country\Data
 * @subpackage      Gateway
 */
interface CountriesGateway
{
    /**
     * Return items filtering results by conditions.
     *
     * @param Request $request
     *
     * @return array
     */
    public function fetch(Request $request);
}
