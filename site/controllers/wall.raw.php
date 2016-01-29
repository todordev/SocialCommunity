<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * SocialCommunity wall controller.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerWall extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    SocialCommunityModelWall    The model.
     * @since    1.5
     */
    public function getModel($name = 'Wall', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * This method store a post by user.
     */
    public function storePost()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $content = $this->input->getString('content');

        $user    = JFactory::getUser();
        $userId  = $user->get('id');

        $content = JString::trim(strip_tags($content));
        $content = JHtmlString::truncate($content, 140);

        if (!$userId) {
            $app->close();
        }

        $userTimeZone = (!$user->getParam('timezone')) ? null : $user->getParam('timezone');

        try {

            $date   = new JDate('now', $userTimeZone);

            $entity = new Socialcommunity\Wall\User\Post(JFactory::getDbo());
            $entity->setUserId($userId);
            $entity->setContent($content);
            $entity->setCreated($date->toSql(true));
            $entity->store();

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $params = JComponentHelper::getParams('com_socialcommunity');
        
        $filesystemHelper  = new Prism\Filesystem\Helper($params);
        $mediaFolder = $filesystemHelper->getMediaFolderUri($userId);
        
        $profile = new Socialcommunity\Profile\Profile(JFactory::getDbo());
        $profile->load(['user_id' => $userId]);

        $displayData = new stdClass();
        $displayData->id = $entity->getId();

        $displayData->profileLink = JRoute::_(SocialCommunityHelperRoute::getProfileRoute($profile->getSlug()), false);
        $displayData->name        = htmlentities($profile->getName(), ENT_QUOTES, 'utf-8');
        $displayData->alias       = htmlentities($profile->getAlias(), ENT_QUOTES, 'utf-8');
        $displayData->imageSquare = $mediaFolder .'/'. $profile->getImageSquare();
        $displayData->imageAlt    = $displayData->name;
        $displayData->content     = $entity->getContent();
        $displayData->created     = JHtml::_('socialcommunity.created', $entity->getCreated(), $userTimeZone);

        $layout      = new JLayoutFile('wall_post');
        
        echo $layout->render($displayData);
        $app->close();
    }

    /**
     * This method removes a post of a user.
     */
    public function removePost()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $itemId = $this->input->getInt('id');
        $userId  = JFactory::getUser()->get('id');

        $validatorOwner = new Socialcommunity\Validator\Post\Owner(JFactory::getDbo(), $itemId, $userId);
        if (!$validatorOwner->isValid()) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_POST'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {

            $entity = new Socialcommunity\Wall\User\Post(JFactory::getDbo());
            $entity->setId($itemId);
            $entity->setUserId($userId);

            $entity->remove();
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $response
            ->setTitle(JText::_('COM_SOCIALCOMMUNITY_SUCCESS'))
            ->setText(JText::_('COM_SOCIALCOMMUNITY_POST_REMOVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }
}
