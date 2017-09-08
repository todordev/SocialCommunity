<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \GraphQL\Type\Schema;
use \GraphQL\Type\Definition\Type;
use \GraphQL\Type\Definition\ObjectType;
use \Socialcommunity\Graph\Type\Post as PostType;
use \Socialcommunity\Graph\Type\Profile as ProfileType;
use \Prism\Database\Condition\Condition;
use \Prism\Database\Condition\Conditions;
use \Prism\Database\Request\Request;
use \Socialcommunity\Post\Post;
use \Prism\Database\Request\Fields;
use \Prism\Database\Request\Field;

// no direct access
defined('_JEXEC') or die;

class SocialcommunityModelProfile extends JModelLegacy
{
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // Visitor
        $visitorId = (int)JFactory::getUser()->get('id');
        $this->setState($this->option . '.visitor.id', $visitorId);

        // If there is no ID in the URI, load profile of the visitor.
        $targetId = $app->input->getUint('id');
        if (!$targetId) {
            $targetId = (int)JFactory::getUser()->get('id');
        }
        $this->setState($this->option . '.target.id', $targetId);

        $visitor = new \Socialcommunity\Profile\Visitor($targetId, $visitorId);
        $this->setState($this->option . '.visitor.is_owner', $visitor->isProfileOwner());
    }

    /**
     * Method to get an object.
     *
     * @param    int $targetId The ID of the user's profile
     * @param    int $isOwner Is the visitor is owner of the profile?
     *
     * @throws   \RuntimeException
     * @return   mixed    Object on success, false on failure.
     */
    public function getItem($targetId, $isOwner)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('a.id, a.name, a.alias, a.image, a.image_square, a.bio, a.website, a.params, a.active, a.user_id')
            ->from($db->quoteName('#__itpsc_profiles', 'a'))
            ->where('a.user_id = ' . (int)$targetId);

        if (!$isOwner) {
            $query->where('a.active = ' . Prism\Constants::ACTIVE);
        }

        $db->setQuery($query, 0, 1);

        $result = $db->loadObject();

        if ($result->params) {
            $result->params = new Joomla\Registry\Registry($result->params);
        }

        return $result;
    }

    public function prepareProfilePosts($userId, $userTimezone)
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'profile' => [
                    'type' => new ProfileType,
                    'args' => [
                        'id' => ['type' => Type::int()]
                    ],
                    'resolve' => function ($root, $args, $context) {
                        return $this->resolveProfile($context['user_id']);
                    }
                ],
                'posts' => [
                    'type' => Type::listOf(new PostType),
                    'resolve' => function ($root, $args, $context) {
                        return $this->resolvePosts($context['user_id'], $context['timezone']);
                    }
                ]
            ]
        ]);

        $result = \GraphQL\GraphQL::executeQuery(
            new Schema([
                'query' => $queryType
            ]),
            '{profile{id, name, alias, image, image_alt, link}, posts{id, content, created_at}}',
            $rootValue = null,
            $context = ['user_id' => $userId, 'timezone' => $userTimezone]
        );

        return $result->toArray();
    }

    /**
     * @param int $userId
     * @param string $userTimezone
     *
     * @return array
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    private function resolvePosts($userId, $userTimezone)
    {
        // Prepare conditions.
        $conditionUserId = new \Prism\Database\Condition\Condition(['column' => 'user_id', 'value' => $userId, 'operator' => '=']);
        $conditions      = new \Prism\Database\Condition\Conditions;
        $conditions->addCondition($conditionUserId);

        // Prepare ordering.
        $orderCreatedAt  = new \Prism\Database\Condition\Order(['column' => 'created_at', 'direction' => 'DESC']);
        $ordering        = new \Prism\Database\Condition\Ordering;
        $ordering->addCondition($orderCreatedAt);

        // Prepare fields.
        $fields     = new Fields;
        $fields
            ->addField(new Field(['column' => 'id', 'table' => 'a']))
            ->addField(new Field(['column' => 'content', 'table' => 'a']))
            ->addField(new Field(['column' => 'created_at', 'table' => 'a']));

        $databaseRequest = new \Prism\Database\Request\Request;
        $databaseRequest
            ->setFields($fields)
            ->setConditions($conditions)
            ->setOrder($ordering);

        $mapper     = new \Socialcommunity\Post\Mapper(new \Socialcommunity\Post\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new \Socialcommunity\Post\Repository($mapper);
        $postsCollection  = $repository->fetchCollection($databaseRequest);

        $wallPosts = array();
        /** @var \Socialcommunity\Post\Post $post */
        foreach ($postsCollection as $post) {
            $wallPosts[] = [
                'id' => $post->getId(),
                'content' => $post->getContent(),
                'created_at' => JHtml::_('socialcommunity.created', $post->createdAt(), $userTimezone)
            ];
        }

        return $wallPosts;
    }

    private function resolveProfile($userId)
    {
        $params           = JComponentHelper::getParams('com_socialcommunity');

        $filesystemHelper = new Prism\Filesystem\Helper($params);
        $mediaFolder      = $filesystemHelper->getMediaFolderUri($userId);

        // Prepare conditions.
        $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
        $conditions      = new Conditions;
        $conditions->addCondition($conditionUserId);

        // Prepare fields.
        $fields     = new Fields;
        $fields
            ->addField(new Field(['column' => 'id', 'table' => 'a']))
            ->addField(new Field(['column' => 'name', 'table' => 'a']))
            ->addField(new Field(['column' => 'alias', 'table' => 'a']))
            ->addField(new Field(['column' => 'image_square', 'table' => 'a']))
            ->addField(new Field(['column' => 'slug', 'table' => 'a', 'is_alias' => Prism\Constants::YES]));

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest
            ->setConditions($conditions)
            ->setFields($fields);

        $mapper     = new \Socialcommunity\Profile\Mapper(new \Socialcommunity\Profile\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new \Socialcommunity\Profile\Repository($mapper);
        $profile    = $repository->fetch($databaseRequest);

        // Prepare profile image.
        if (!$profile->getImageSquare()) {
            $image    = 'media/com_socialcommunity/images/no_profile_50x50.png';
            $imageAlt = '';
        } else {
            $image    = $mediaFolder .'/'. $profile->getImageSquare();
            $imageAlt = $profile->getName();
        }

        return [
            'id' => $profile->getId(),
            'name' => $profile->getName(),
            'alias' => $profile->getAlias(),
            'image' => $image,
            'image_alt' => $imageAlt,
            'link' => JRoute::_(SocialcommunityHelperRoute::getProfileRoute($profile->getSlug()), false),
        ];
    }

    public function preparePost(Post $post, $userTimezone)
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'post' => [
                    'type' => new PostType,
                    'resolve' => function ($root, $args, $context) {
                        return $this->resolvePost($context['post'], $context['timezone']);
                    }
                ]
            ]
        ]);

        $result = \GraphQL\GraphQL::executeQuery(
            new Schema([
                'query' => $queryType
            ]),
            '{post{id, content, created_at}}',
            $rootValue = null,
            $context = [
                'post' => $post,
                'timezone' => $userTimezone
            ]
        );

        return $result->toArray();
    }

    private function resolvePost(Post $post, $userTimezone)
    {
        return [
            'id' => $post->getId(),
            'content' => $post->getContent(),
            'created_at' => JHtml::_('socialcommunity.created', $post->createdAt(), $userTimezone)
        ];
    }
}
