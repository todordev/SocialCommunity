<?php
/**
 * @package      Socialcommunity\Validator
 * @subpackage   Post
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Post;

use Prism\Validator\ValidatorInterface;
use Socialcommunity\Validator\Post\Gateway\OwnerGateway;

/**
 * This class provides functionality for validation notification owner.
 *
 * @package      Socialcommunity\Validator
 * @subpackage   Post
 */
class Owner implements ValidatorInterface
{
    /**
     * @var OwnerGateway
     */
    protected $gateway;

    protected $postId;
    protected $userId;

    /**
     * Initialize the object.
     *
     * <code>
     * $postId = 1;
     * $userId = 2;
     *
     * $postOwner = new Socialcommunity\Validator\Post\Owner($postId, $userId);
     * $postOwner->setGateway(new Socialcommunity\Validator\Post\Gateway\Joomla\Owner(\JFactory::getDbo()));
     * </code>
     *
     * @param int $postId Item ID.
     * @param int $userId User ID.
     */
    public function __construct($postId, $userId)
    {
        $this->postId        = $postId;
        $this->userId    = $userId;
    }

    /**
     * Set database gateway.
     *
     * @param OwnerGateway $gateway
     */
    public function setGateway(OwnerGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Validate post owner.
     *
     * <code>
     * $postId = 1;
     * $userId = 2;
     *
     * $postOwner = new Socialcommunity\Validator\Post\Owner($postId, $userId);
     * $postOwner->setGateway(new Socialcommunity\Validator\Post\Gateway\Joomla\Owner(\JFactory::getDbo()));
     *
     * if($postOwner->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->gateway->isOwner($this->postId, $this->userId);
    }
}
