<?php
/**
 * @package      Socialcommunity\Validator
 * @subpackage   Profile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Profile;

use Prism\Validator\ValidatorInterface;
use Socialcommunity\Validator\Profile\Gateway\AliasGateway;

/**
 * This class provides functionality for validation notification owner.
 *
 * @package      Socialcommunity\Validator
 * @subpackage   Profile
 */
class Alias implements ValidatorInterface
{
    /**
     * @var AliasGateway
     */
    protected $gateway;

    protected $user_id;
    protected $alias;

    /**
     * Initialize the object.
     *
     * <code>
     * $userId = 1;
     * $alias = 'john-dow';
     *
     * $profileAlias = new Socialcommunity\Validator\Profile\Alias($alias, $userId);
     * $profileAlias->setGateway(new Socialcommunity\Validator\Profile\Gateway\Joomla\Alias(\JFactory::getDbo()));
     * </code>
     *
     * @param string          $alias
     * @param int             $userId
     */
    public function __construct($alias, $userId = 0)
    {
        $this->user_id   = $userId;
        $this->alias     = $alias;
    }

    /**
     * Set database gateway.
     *
     * @param AliasGateway $gateway
     */
    public function setGateway(AliasGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Check for valid profile alias or if it exists.
     *
     * <code>
     * $userId = 1;
     * $alias = 'john-dow';
     *
     * $profileAlias = new Socialcommunity\Validator\Profile\Alias($alias, $userId);
     * $profileAlias->setGateway(new Socialcommunity\Validator\Profile\Gateway\Joomla\Alias(\JFactory::getDbo()));
     *
     * if($profileAlias->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function isValid()
    {
        if (!$this->alias or preg_match('/[^A-Z0-9\-]/i', $this->alias)) {
            return false;
        }

        $result = $this->gateway->isExists($this->alias, $this->user_id);

        return (bool)!$result;
    }
}
