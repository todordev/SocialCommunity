<?php
/**
 * @package      Socialcommunity
 * @subpackage   Profile
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile;

/**
 * Value object of the visitor.
 *
 * @package      Socialcommunity
 * @subpackage   Profile
 */
class Visitor
{
    /**
     * The user ID of the profile owner.
     *
     * @var int $targetId
     */
    protected $targetId;

    /**
     * The user ID of the profile visitor.
     *
     * @var int $visitor
     */
    protected $visitorId;

    /**
     * Visitor constructor.
     *
     * @param int $targetId
     * @param int $visitorId
     */
    public function __construct($targetId, $visitorId)
    {
        $this->targetId = (int)$targetId;
        $this->visitorId = (int)$visitorId;
    }

    /**
     * Check if it is profile owner.
     *
     * @return bool
     */
    public function isProfileOwner()
    {
        return (bool)($this->targetId === $this->visitorId);
    }
}
