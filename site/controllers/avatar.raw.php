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
 * Avatar controller class.
 *
 * @package     SocialCommunity
 * @subpackage  Components
 */
class SocialCommunityControllerAvatar extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   SocialCommunityModelAvatar    The model.
     * @since    1.5
     */
    public function getModel($name = 'Avatar', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function upload()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $userId = JFactory::getUser()->get('id');

        $response = new Prism\Response\Json();

        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_PROFILE'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $image    = $this->input->files->get('profile_image', array(), 'array');
            $fileUrl  = null;

            // Upload image
            if (!empty($image['name'])) {
                $params = JComponentHelper::getParams('com_socialcommunity');

                $filesystemHelper = new Prism\Filesystem\Helper($params);

                $model      = $this->getModel();
                $sourceFile = $model->uploadImage($image, $filesystemHelper->getTemporaryMediaFolder(JPATH_ROOT));

                if ($sourceFile) {
                    $filename = basename($sourceFile);
                    $fileUrl  = JUri::base() . $filesystemHelper->getTemporaryMediaFolderUri() . '/' . $filename;
                    $app->setUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT, $filename);
                }
            }

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        if (!$fileUrl) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANNOT_BE_UPLOADED'))
                ->failure();
        } else {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_SUCCESS'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_FILE_UPLOADED_SUCCESSFULLY'))
                ->setData(array('image' => $fileUrl))
                ->success();
        }

        echo $response;
        $app->close();
    }

    public function cancelImageCrop()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $oldImage = $app->getUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT);
            if (Joomla\String\StringHelper::strlen($oldImage) > 0) {
                $params = JComponentHelper::getParams('com_socialcommunity');

                $filesystemHelper = new Prism\Filesystem\Helper($params);

                // Get the folder where the images will be stored
                $temporaryFolder = $filesystemHelper->getTemporaryMediaFolder(JPATH_ROOT);
                $oldImage = JPath::clean($temporaryFolder .'/'. basename($oldImage), '/');

                if (JFile::exists($oldImage)) {
                    JFile::delete($oldImage);
                }
            }

            // Set the name of the image in the session.
            $app->setUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT, null);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response
            ->setTitle(JText::_('COM_SOCIALCOMMUNITY_SUCCESS'))
            ->setText(JText::_('COM_SOCIALCOMMUNITY_FILE_REMOVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }

    public function cropImage()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        // Get the model
        $model = $this->getModel();
        /** @var $model SocialcommunityModelAvatar */

        $params = JComponentHelper::getParams('com_socialcommunity');

        $filesystemHelper = new Prism\Filesystem\Helper($params);

        // Get the filename from the session.
        $fileName           = basename($app->getUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT));
        $temporaryFolder    = $filesystemHelper->getTemporaryMediaFolder(JPATH_ROOT);
        $temporaryFile      = JPath::clean($temporaryFolder .'/'. $fileName, '/');

        if (!$fileName or !JFile::exists($temporaryFile)) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_DOES_NOT_EXIST'))
                ->failure();

            echo $response;
            $app->close();
        }

        $imageUrl = '';

        try {
            $options = array(
                'width'    => $this->input->getFloat('width'),
                'height'   => $this->input->getFloat('height'),
                'x'        => $this->input->getFloat('x'),
                'y'        => $this->input->getFloat('y'),
                'destination'   => $temporaryFolder,
            );

            // Resize the picture.
            $images              = $model->cropImage($temporaryFile, $options, $params);

            $temporaryAdapter    = new League\Flysystem\Adapter\Local($temporaryFolder);
            $temporaryFilesystem = new League\Flysystem\Filesystem($temporaryAdapter);

            $storageFilesystem   = $filesystemHelper->getFilesystem();

            $manager = new League\Flysystem\MountManager([
                'temporary' => $temporaryFilesystem,
                'storage'   => $storageFilesystem
            ]);

            $mediaFolder       = $filesystemHelper->getMediaFolder($userId);

            $model->moveImages($images, $manager, $mediaFolder);

            $model->storeImages($userId, $images, $storageFilesystem, $mediaFolder);

            // Prepare URL to the image.
            $imageName  = basename(Joomla\Utilities\ArrayHelper::getValue($images, 'image_profile'));
            $imageUrl   = $filesystemHelper->getMediaFolderUri($userId) . '/' . $imageName;

            $app->setUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT, null);

        } catch (RuntimeException $e) {
            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText($e->getMessage())
                ->failure();

            echo $response;
            $app->close();

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');

            $response
                ->setTitle(JText::_('COM_SOCIALCOMMUNITY_FAILURE'))
                ->setText(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $response
            ->setTitle(JText::_('COM_SOCIALCOMMUNITY_SUCCESS'))
            ->setText(JText::_('COM_SOCIALCOMMUNITY_IMAGE_SAVED'))
            ->setData($imageUrl)
            ->success();

        echo $response;
        $app->close();
    }
}
