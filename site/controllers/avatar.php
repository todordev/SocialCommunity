<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('Prism.libs.GuzzleHttp.init');
jimport('Prism.libs.Aws.init');

/**
 * Form controller class.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerAvatar extends Prism\Controller\DefaultController
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialCommunityModelAvatar|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Avatar', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function removeImage()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );

            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        $redirectOptions = array(
            'view' => 'form',
            'layout' => 'avatar'
        );

        try {
            $params = JComponentHelper::getParams('com_socialcommunity');

            $filesystemHelper    = new Prism\Filesystem\Helper($params);

            $storageFilesystem   = $filesystemHelper->getFilesystem();
            $mediaFolder         = $filesystemHelper->getMediaFolder($userId);

            $model = $this->getModel();
            $model->removeImage($userId, $mediaFolder, $storageFilesystem);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_IMAGE_DELETED'), $redirectOptions);
    }
}
