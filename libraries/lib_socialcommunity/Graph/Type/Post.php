<?php
/**
 * @package      Socialcommunity
 * @subpackage   Post
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Graph\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * This is a class that provides functionality for managing user post.
 *
 * @package      Socialcommunity
 * @subpackage   Post
 */
class Post extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Post',
            'description' => 'User post',
            'fields' => [
                'id' => ['type' => Type::int()],
                'content' => ['type' => Type::string()],
                'created_at' => ['type' => Type::string()]
            ]
        ];

        parent::__construct($config);
    }
}
