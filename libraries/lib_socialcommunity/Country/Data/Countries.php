<?php
/**
 * @package      Socialcommunity\Country
 * @subpackage   Data
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Country\Data;

use Prism\Database\Request\Request;
use Prism\Domain\CollectionToOptions;
use Prism\Domain\ToOptionsMethod;
use Socialcommunity\Country\Data\Gateway\CountriesGateway;

/**
 * This class provides functionality fetches country data and return it as options.
 *
 * @package      Socialcommunity\Country
 * @subpackage   Data
 */
class Countries implements CollectionToOptions
{
    use ToOptionsMethod;

    protected $items = array();

    /**
     * Database gateway.
     *
     * @var CountriesGateway
     */
    protected $gateway;

    public function __construct(CountriesGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Fetch the data from database.
     *
     * @param Request $request
     */
    public function load(Request $request)
    {
        $this->items = $this->gateway->fetch($request);
    }

    /**
     * Return the results as array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }
}
