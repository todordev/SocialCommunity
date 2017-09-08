<?php
/**
 * @package      Socialcommunity\Post
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Post\Command;

use Prism\Command\Command;
use Socialcommunity\Post\Post;
use Socialcommunity\Post\Command\Gateway\UpdateContentGateway;

/**
 * Update Post content command.
 *
 * @package      Socialcommunity\Post
 * @subpackage   Command
 */
class UpdateContent implements Command
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * @var UpdateContentGateway
     */
    protected $gateway;

    /**
     * Update Post content command constructor.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post     = $post;
    }

    /**
     * @param UpdateContentGateway $gateway
     *
     * @return self
     */
    public function setGateway(UpdateContentGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->update($this->post);
    }
}
