<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command;

use Prism\Command\Command;
use Socialcommunity\Value\Profile\Image;
use Socialcommunity\Profile\Command\Gateway\UpdateImageGateway;

/**
 * Update profile image.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
class UpdateImage implements Command
{
    /**
     * @var UpdateImageGateway
     */
    protected $gateway;

    /**
     * @var Image
     */
    protected $profileImage;

    /**
     * Store basic profile data command constructor.
     *
     * @param Image $profileImage
     */
    public function __construct(Image $profileImage)
    {
        $this->profileImage = $profileImage;
    }

    /**
     * @param UpdateImageGateway $gateway
     *
     * @return self
     */
    public function setGateway(UpdateImageGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->update($this->profileImage);
    }
}
