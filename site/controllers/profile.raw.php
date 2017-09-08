<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Socialcommunity Profile controller.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerProfile extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    SocialcommunityModelProfile|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Profile', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Return user's posts.
     *
     * @throws \Exception
     */
    public function posts()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $user   = JFactory::getUser();
        $userId = $user->get('id');

        if (!$userId) {
            $response->failure();
            echo $response;
            $app->close();
        }

        $userTimezone = $user->getParam('timezone') ?: null;

        $result = array();

        try {
            $model  = $this->getModel();
            $result = $model->prepareProfilePosts($userId, $userTimezone);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');

            $response
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response->success();
        if (array_key_exists('data', $result)) {
            $response->setData($result['data']);
        }

        echo $response;
        $app->close();
    }


    /**
     * This method store a post by user.
     *
     * @throws \Exception
     */
    public function storePost()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $user   = JFactory::getUser();
        $userId = (int)$user->get('id');

        if (!$userId) {
            $response->failure();
            echo $response;
            $app->close();
        }

        $content = $this->input->getString('content');
        $content = Joomla\String\StringHelper::trim(strip_tags($content));
        $content = JHtmlString::truncate($content, 140);

        if (!$content) {
            $response->failure();
            echo $response;
            $app->close();
        }

        $result       = array();
        $userTimezone = $user->getParam('timezone') ?: null;

        try {
            $date = new JDate('now', $userTimezone);

            $post = new Socialcommunity\Post\Post();
            $post->setUserId($userId);
            $post->setContent($content);
            $post->setCreatedAt($date->toSql(true));

            $mapper     = new \Socialcommunity\Post\Mapper(new \Socialcommunity\Post\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new \Socialcommunity\Post\Repository($mapper);
            $repository->store($post);

            $model  = $this->getModel();
            $result = $model->preparePost($post, $userTimezone);
        } catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');

            $response
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response->success();
        if (array_key_exists('data', $result)) {
            $response->setData($result['data']);
        }

        echo $response;
        $app->close();
    }

    /**
     * This method store a post by user.
     *
     * @throws \Exception
     */
    public function updatePost()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $user   = JFactory::getUser();
        $userId = (int)$user->get('id');
        $id     = $this->input->post->getUint('id');

        if (!$userId or !$id) {
            $response->failure();
            echo $response;
            $app->close();
        }

        $content = $this->input->getString('content');
        $content = Joomla\String\StringHelper::trim(strip_tags($content));
        $content = JHtmlString::truncate($content, 140);

        if (!$content) {
            $response->failure();
            echo $response;
            $app->close();
        }

        try {
            $post = new Socialcommunity\Post\Post();
            $post->setId($id);
            $post->setUserId($userId);
            $post->setContent($content);

            $command    = new \Socialcommunity\Post\Command\UpdateContent($post);
            $command->setGateway(new \Socialcommunity\Post\Command\Gateway\Joomla\UpdateContent(JFactory::getDbo()));
            $command->handle();
        } catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');

            $response
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response
            ->setContent(JText::_('COM_SOCIALCOMMUNITY_POST_UPDATE_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }

    /**
     * This method removes a post of a user.
     *
     * @throws \Exception
     */
    public function removePost()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $itemId = $this->input->getUint('id');
        $userId = JFactory::getUser()->get('id');

        $validatorOwner = new Socialcommunity\Validator\Post\Owner($itemId, $userId);
        $validatorOwner->setGateway(new \Socialcommunity\Validator\Post\Gateway\Joomla\Owner(JFactory::getDbo()));
        if (!$validatorOwner->isValid()) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_POST'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $post = new Socialcommunity\Post\Post;
            $post->setId($itemId);
            $post->setUserId($userId);

            $mapper     = new \Socialcommunity\Post\Mapper(new \Socialcommunity\Post\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new \Socialcommunity\Post\Repository($mapper);
            $repository->delete($post);
        } catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');

            $response
                ->setContent(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response
            ->setContent(JText::_('COM_SOCIALCOMMUNITY_POST_REMOVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }
}
