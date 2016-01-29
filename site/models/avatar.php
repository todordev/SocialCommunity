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

class SocialCommunityModelAvatar extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string   $type The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialCommunityTableProfile  A database object
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
     * @param array $image Array with information about uploaded file.
     * @param string $destination
     *
     * @throws RuntimeException
     * @return array
     */
    public function uploadImage($image, $destination)
    {
        $app           = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $uploadedFile  = Joomla\Utilities\ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName  = Joomla\Utilities\ArrayHelper::getValue($image, 'name');
        $errorCode     = Joomla\Utilities\ArrayHelper::getValue($image, 'error');

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams   = JComponentHelper::getParams('com_media');

        $file          = new Prism\File\Image($image, $destination);

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get('upload_maxsize') * $KB;

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

        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        return $file->upload();
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
        $image = new JImage();
        $image->loadFile($file);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND', $file));
        }

        $destinationFolder  = Joomla\Utilities\ArrayHelper::getValue($options, 'destination');

        // Generate temporary file name
        $generatedName = Prism\Utilities\StringHelper::generateRandomString(24);

        $profileName  = $generatedName . '_profile.png';
        $smallName    = $generatedName . '_small.png';
        $squareName   = $generatedName . '_square.png';
        $iconName     = $generatedName . '_icon.png';

        $imageFile  = JPath::clean($destinationFolder . DIRECTORY_SEPARATOR . $profileName);
        $smallFile  = JPath::clean($destinationFolder . DIRECTORY_SEPARATOR . $smallName);
        $squareFile = JPath::clean($destinationFolder . DIRECTORY_SEPARATOR . $squareName);
        $iconFile   = JPath::clean($destinationFolder . DIRECTORY_SEPARATOR . $iconName);

        // Create profile image.
        $width  = Joomla\Utilities\ArrayHelper::getValue($options, 'width', 200);
        $width  = ($width < 25) ? 50 : $width;
        $height = Joomla\Utilities\ArrayHelper::getValue($options, 'height', 200);
        $height = ($height < 25) ? 50 : $height;
        $left   = Joomla\Utilities\ArrayHelper::getValue($options, 'x', 0);
        $top    = Joomla\Utilities\ArrayHelper::getValue($options, 'y', 0);
        $image->crop($width, $height, $left, $top, false);

        // Resize to general size.
        $width  = $params->get('image_width', 200);
        $width  = ($width < 25) ? 50 : $width;
        $height = $params->get('image_height', 200);
        $height = ($height < 25) ? 50 : $height;
        $image->resize($width, $height, false);

        // Store to file.
        $image->toFile($imageFile, IMAGETYPE_PNG);

        // Create small image.
        $width  = $params->get('image_small_width', 100);
        $height = $params->get('image_small_height', 100);
        $image->resize($width, $height, false);
        $image->toFile($smallFile, IMAGETYPE_PNG);

        // Create square image.
        $width  = $params->get('image_square_width', 50);
        $height = $params->get('image_square_height', 50);
        $image->resize($width, $height, false);
        $image->toFile($squareFile, IMAGETYPE_PNG);

        // Create icon image.
        $width  = $params->get('image_icon_width', 25);
        $height = $params->get('image_icon_height', 25);
        $image->resize($width, $height, false);
        $image->toFile($iconFile, IMAGETYPE_PNG);

        $names = array(
            'image_profile'  => $profileName,
            'image_small'    => $smallName,
            'image_square'   => $squareName,
            'image_icon'     => $iconName
        );

        // Remove the temporary file.
        if (JFile::exists($file)) {
            JFile::delete($file);
        }

        return $names;
    }

    /**
     * Crop the image and generates smaller ones.
     *
     * @param array $images
     * @param string $mediaFolder
     * @param League\Flysystem\MountManager $manager
     *
     * @throws Exception
     *
     * @return array
     */
    public function moveImages($images, $mediaFolder, $manager)
    {
        // Resize image
        if (!$images) {
            throw new Exception(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILES_NOT_FOUND_S', var_export($images, true)));
        }

        foreach ($images as $fileName) {

            if (!$manager->has('temporary://'.$fileName)) {
                throw new Exception(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND_S', $fileName));
            }

            $manager->move('temporary://'.$fileName, 'storage://'. $mediaFolder .'/'. $fileName);
        }
    }

    /**
     * Method to save the images in profile record.
     *
     * @param    array    $images
     * @param    int      $userId
     * @param    string   $mediaFolder
     * @param    League\Flysystem\Filesystem  $filesystem
     */
    public function storeImages($userId, $images, $mediaFolder, $filesystem)
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
