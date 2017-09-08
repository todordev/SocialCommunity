<?php
/**
 * @package      Socialcommunity
 * @subpackage   Countries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Country;

use Prism\Domain\Collection;
use Prism\Domain\CollectionToOptions;
use Prism\Domain\ToOptionsMethod;

/**
 * This class provides functionality that manage locations.
 *
 * @package      Socialcommunity
 * @subpackage   Countries
 */
class Countries extends Collection implements CollectionToOptions
{
    use ToOptionsMethod;
}
