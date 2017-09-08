<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;
use Socialcommunity\Profile\Profile;
use League\Flysystem\Filesystem;
use Socialcommunity\Value\Profile\Image as ProfileImage;

// no direct access
defined('_JEXEC') or die;

class SocialcommunityModelAvatar extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string   $type The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialcommunityTableProfile|bool  A database object
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialcommunityTable', $config = array())
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
     * @param string $destinationFolder
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function uploadImage($uploadedFileData, $destinationFolder)
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

        // Prepare image size validator.
        $params             = JComponentHelper::getParams('com_socialcommunity');
        $imageSizeValidator = new Prism\File\Validator\Image\Size($uploadedFile);
        $imageSizeValidator->setMinWidth($params->get('image_width', 200));
        $imageSizeValidator->setMinHeight($params->get('image_height', 200));

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($imageSizeValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Upload the file in temporary folder.
        $options = new Registry(array(
            'filename_length'  => 16,
            'image_type'       => \JFile::getExt($uploadedName)
        ));

        $file     = new Prism\File\Image($uploadedFile);
        $fileData = $file->toFile($destinationFolder, $options);

        if (!JFile::exists($fileData['filepath'])) {
            throw new RuntimeException('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED');
        }

        return $fileData;
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
        $generatedName = Prism\Utilities\StringHelper::generateRandomString(16);

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
     * @param    Profile  $profile
     * @param    array    $images
     * @param    Filesystem  $filesystem
     * @param    string   $mediaFolder
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function storeImages($profile, $images, Filesystem $filesystem, $mediaFolder)
    {
        if ($profile->getId() and count($images) > 0) {
            // Delete old profile images.
            $profileImage               = new ProfileImage;
            $profileImage->image        = $profile->getImage();
            $profileImage->image_icon   = $profile->getImageIcon();
            $profileImage->image_small  = $profile->getImageSmall();
            $profileImage->image_square = $profile->getImageSquare();

            $this->deleteImage($profileImage, $filesystem, $mediaFolder);

            // Store the new profile image.
            $profileImage               = new ProfileImage;
            $profileImage->profile_id   = (int)$profile->getId();
            $profileImage->image        = $images['image_profile'];
            $profileImage->image_icon   = $images['image_icon'];
            $profileImage->image_small  = $images['image_small'];
            $profileImage->image_square = $images['image_square'];

            $commandUpdateImage = new \Socialcommunity\Profile\Command\UpdateImage($profileImage);
            $commandUpdateImage->setGateway(new \Socialcommunity\Profile\Command\Gateway\Joomla\UpdateImage(JFactory::getDbo()));
            $commandUpdateImage->handle();
        }
    }

    /**
     * Delete the profile picture.
     *
     * @param Profile $profile
     * @param Filesystem  $filesystem
     * @param string $mediaFolder
     *
     * @throws Exception
     */
    public function removeImage($profile, Filesystem $filesystem, $mediaFolder)
    {
        if ($profile->getId() > 0) {
            // Remove old profile image.
            $profileImage               = new ProfileImage;
            $profileImage->image        = $profile->getImage();
            $profileImage->image_icon   = $profile->getImageIcon();
            $profileImage->image_small  = $profile->getImageSmall();
            $profileImage->image_square = $profile->getImageSquare();

            $this->deleteImage($profileImage, $filesystem, $mediaFolder);

            // Initialize the value object of the profile image.
            $profileImage             = new ProfileImage;
            $profileImage->profile_id = $profile->getId();

            // Store blank value to the profile images record.
            $commandUpdateImage = new \Socialcommunity\Profile\Command\UpdateImage($profileImage);
            $commandUpdateImage->setGateway(new \Socialcommunity\Profile\Command\Gateway\Joomla\UpdateImage(JFactory::getDbo()));
            $commandUpdateImage->handle();
        }
    }

    /**
     * @param ProfileImage $profileImage
     * @param Filesystem $filesystem
     * @param string     $mediaFolder
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function deleteImage(ProfileImage $profileImage, Filesystem $filesystem, $mediaFolder)
    {
        // Delete the profile pictures.
        if ($profileImage->image and $filesystem->has($mediaFolder . '/' . $profileImage->image)) {
            $filesystem->delete($mediaFolder . '/' . $profileImage->image);
        }

        if ($profileImage->image_small and $filesystem->has($mediaFolder . '/' . $profileImage->image_small)) {
            $filesystem->delete($mediaFolder . '/' . $profileImage->image_small);
        }

        if ($profileImage->image_square and $filesystem->has($mediaFolder . '/' . $profileImage->image_square)) {
            $filesystem->delete($mediaFolder . '/' . $profileImage->image_square);
        }

        if ($profileImage->image_icon and $filesystem->has($mediaFolder . '/' . $profileImage->image_icon)) {
            $filesystem->delete($mediaFolder . '/' . $profileImage->image_icon);
        }
    }

    /**
     * Remove the temporary images if a user upload or crop a picture,
     * but he does not store it or reload the page.
     *
     * @param JApplicationSite $app
     *
     * @throws \Exception
     */
    public function removeTemporaryImage($app)
    {
        // Remove old image if it exists.
        $oldImage = (string)$app->getUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT);
        if ($oldImage !== '') {
            $params           = JComponentHelper::getParams('com_socialcommunity');
            $filesystemHelper = new Prism\Filesystem\Helper($params);

            $temporaryFolder = $filesystemHelper->getTemporaryMediaFolder(JPATH_ROOT);
            $oldImage = JPath::clean($temporaryFolder .'/'. basename($oldImage), '/');
            if (JFile::exists($oldImage)) {
                JFile::delete($oldImage);
            }
        }

        // Set the name of the image in the session.
        $app->setUserState(Socialcommunity\Constants::TEMPORARY_IMAGE_CONTEXT, null);
    }
}
