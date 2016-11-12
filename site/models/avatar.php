<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

class SocialCommunityModelAvatar extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string   $type The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialCommunityTableProfile|bool  A database object
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialCommunityTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

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

        // Load the parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
     *
     * @return    mixed   Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        $item = null;

        if ($id > 0) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('a.id, a.image, a.image_icon, a.image_square, a.image_small')
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->where('a.user_id = ' . (int)$id);

            $db->setQuery($query, 0, 1);
            $item = $db->loadObject();
        }

        return $item;
    }

    /**
     * Upload an image
     *
     * @param array $uploadedFileData Array with information about uploaded file.
     * @param string $destination
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function uploadImage($uploadedFileData, $destination)
    {
        $uploadedFile  = ArrayHelper::getValue($uploadedFileData, 'tmp_name');
        $uploadedName  = ArrayHelper::getValue($uploadedFileData, 'name');
        $errorCode     = ArrayHelper::getValue($uploadedFileData, 'error');

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams   = JComponentHelper::getParams('com_media');

        // Prepare size validator.
        $KB            = pow(1024, 2);
        $uploadMaxSize = $mediaParams->get('upload_maxsize') * $KB;
        $fileSize      = ArrayHelper::getValue($uploadedFileData, 'size', 0, 'int');

        // Prepare file size validator
        $sizeValidator = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode);

        // Prepare image validator.
        $imageValidator = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(',', $mediaParams->get('upload_mime'));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(',', $mediaParams->get('image_extensions'));
        $imageValidator->setImageExtensions($imageExtensions);

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Upload the file in temporary folder.
        $filesystemLocal = new Prism\Filesystem\Adapter\Local($destination);
        $sourceFile      = $filesystemLocal->upload($uploadedFileData);

        if (!JFile::exists($sourceFile)) {
            throw new RuntimeException('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED');
        }

        return $sourceFile;
    }

    /**
     * Crop the image and generates smaller ones.
     *
     * @param string $file
     * @param array $options
     * @param Joomla\Registry\Registry $params
     *
     * @throws Exception
     *
     * @return array
     */
    public function cropImage($file, $options, $params)
    {
        // Resize image
        $image              = new \Prism\File\Image($file);
        $destinationFolder  = ArrayHelper::getValue($options, 'destination');

        // Generate temporary file name
        $generatedName = Prism\Utilities\StringHelper::generateRandomString(24);

        // Crop the image.
        $imageOptions = new Registry;
        $imageOptions->set('create_new', Prism\Constants::NO);
        $imageOptions->set('filename', $generatedName);
        $imageOptions->set('quality', $params->get('image_quality', Prism\Constants::QUALITY_VERY_HIGH));

        // Prepare width.
        $width  = ArrayHelper::getValue($options, 'width', 200);
        $width  = ($width < 25) ? 50 : $width;
        $imageOptions->set('width', $width);

        // Prepare height.
        $height = ArrayHelper::getValue($options, 'height', 200);
        $height = ($height < 25) ? 50 : $height;
        $imageOptions->set('height', $height);

        // Prepare starting points x and y.
        $left   = ArrayHelper::getValue($options, 'x', 0);
        $imageOptions->set('x', $left);
        $top    = ArrayHelper::getValue($options, 'y', 0);
        $imageOptions->set('y', $top);

        // Crop the image.
        $fileData = $image->crop($destinationFolder, $imageOptions);
        $image    = new Prism\File\Image($fileData['filepath']);
        $croppedImageFilepath = $fileData['filepath'];

        // Resize to general size.
        $imageOptions->set('suffix', '_image');
        $width  = ArrayHelper::getValue($options, 'resize_width', 200);
        $width  = ($width < 25) ? 50 : $width;
        $imageOptions->set('width', $width);
        $height = ArrayHelper::getValue($options, 'resize_height', 200);
        $height = ($height < 25) ? 50 : $height;
        $imageOptions->set('height', $height);

        $fileData     = $image->resize($destinationFolder, $imageOptions);
        $profileImage = $fileData['filename'];

        // Create small image.
        $imageOptions->set('suffix', '_small');
        $imageOptions->set('width', $params->get('image_small_width', 100));
        $imageOptions->set('height', $params->get('image_small_height', 100));
        $fileData   = $image->resize($destinationFolder, $imageOptions);
        $smallImage = $fileData['filename'];

        // Create square image.
        $imageOptions->set('suffix', '_square');
        $imageOptions->set('width', $params->get('image_square_width', 50));
        $imageOptions->set('height', $params->get('image_square_height', 50));
        $fileData    = $image->resize($destinationFolder, $imageOptions);
        $squareImage = $fileData['filename'];

        // Create icon image.
        $imageOptions->set('suffix', '_icon');
        $imageOptions->set('width', $params->get('image_icon_width', 25));
        $imageOptions->set('height', $params->get('image_icon_height', 25));
        $fileData   = $image->resize($destinationFolder, $imageOptions);
        $iconImage  = $fileData['filename'];

        $names = array(
            'image_profile'  => $profileImage,
            'image_small'    => $smallImage,
            'image_square'   => $squareImage,
            'image_icon'     => $iconImage
        );

        // Remove the temporary file.
        if (JFile::exists($file)) {
            JFile::delete($file);
        }

        if (JFile::exists($croppedImageFilepath)) {
            JFile::delete($croppedImageFilepath);
        }

        return $names;
    }

    /**
     * Move the images to file system.
     *
     * @param array $images
     * @param League\Flysystem\MountManager $manager
     * @param string $mediaFolder
     *
     * @throws Exception
     *
     * @return array
     */
    public function moveImages($images, $manager, $mediaFolder)
    {
        // Resize image
        if (!$images) {
            throw new RuntimeException(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILES_NOT_FOUND_S', var_export($images, true)));
        }

        foreach ($images as $fileName) {
            if (!$manager->has('temporary://'.$fileName)) {
                throw new RuntimeException(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND_S', $fileName));
            }

            $manager->move('temporary://'.$fileName, 'storage://'. $mediaFolder .'/'. $fileName);
        }
    }

    /**
     * Method to save the images in profile record.
     *
     * @param    array    $images
     * @param    int      $userId
     * @param    League\Flysystem\Filesystem  $filesystem
     * @param    string   $mediaFolder
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function storeImages($userId, $images, $filesystem, $mediaFolder)
    {
        // Load a record from the database
        $row = $this->getTable();
        $row->load(array('user_id' => $userId));

        if ($row->get('image') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image'))) {
            $filesystem->delete($mediaFolder .'/'. $row->get('image'));
        }

        if ($row->get('image_small') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_small'))) {
            $filesystem->delete($mediaFolder .'/'. $row->get('image_small'));
        }

        if ($row->get('image_square') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_square'))) {
            $filesystem->delete($mediaFolder .'/'. $row->get('image_square'));
        }

        if ($row->get('image_icon') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_icon'))) {
            $filesystem->delete($mediaFolder .'/'. $row->get('image_icon'));
        }

        $row->set('image', $images['image_profile']);
        $row->set('image_small', $images['image_small']);
        $row->set('image_square', $images['image_square']);
        $row->set('image_icon', $images['image_icon']);

        $row->store();
    }

    /**
     * Delete the profile picture of the user.
     *
     * @param int $userId
     * @param string $mediaFolder
     * @param League\Flysystem\Filesystem  $filesystem
     *
     * @throws Exception
     */
    public function removeImage($userId, $mediaFolder, $filesystem)
    {
        $row = $this->getTable();
        $row->load(array(
            'user_id' => $userId
        ));

        if ((int)$row->get('id') > 0) {
            // Delete the profile pictures.
            if ($row->get('image') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image'))) {
                $filesystem->delete($mediaFolder .'/'. $row->get('image'));
            }

            if ($row->get('image_small') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_small'))) {
                $filesystem->delete($mediaFolder .'/'. $row->get('image_small'));
            }

            if ($row->get('image_square') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_square'))) {
                $filesystem->delete($mediaFolder .'/'. $row->get('image_square'));
            }

            if ($row->get('image_icon') !== '' and $filesystem->has($mediaFolder .'/'. $row->get('image_icon'))) {
                $filesystem->delete($mediaFolder .'/'. $row->get('image_icon'));
            }

            $row->set('image', '');
            $row->set('image_small', '');
            $row->set('image_square', '');
            $row->set('image_icon', '');
            $row->store();
        }
    }
}
