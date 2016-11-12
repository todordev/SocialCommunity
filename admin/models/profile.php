<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

// Register Observers
JLoader::register('SocialcommunityObserverProfile', SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . '/tables/observers/profile.php');
JObserverMapper::addObserverClassToClass('SocialcommunityObserverProfile', 'SocialcommunityTableProfile', array('typeAlias' => 'com_socialcommunity.profile'));

/**
 * This model provides functionality for managing user profile.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityModelProfile extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialCommunityTableProfile|bool
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialcommunityTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @throws \Exception
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.profile.data', array());

        if (!$data) {
            $data = $this->getItem();

            if ($data !== null and $data->id > 0) {
                // Prepare social profiles

                $socialProfiles = new Socialcommunity\Profile\SocialProfiles($this->getDbo());
                $socialProfiles->load(array('user_id' => $data->user_id));
                if (count($socialProfiles) > 0) {
                    foreach ($socialProfiles as $item) {
                        $type = $item['type'];
                        $data->$type = $item['alias'];
                    }
                }

                // Prepare locations
                if ($data->location_id > 0) {
                    $location = new Socialcommunity\Location\Location(JFactory::getDbo());
                    $location->load($data->location_id);

                    $locationName = $location->getName(Socialcommunity\Constants::INCLUDE_COUNTRY_CODE);

                    if ($locationName !== '') {
                        $data->location_preview = $locationName;
                    }
                }

                $secretKey  = JFactory::getApplication()->get('secret');

                $data->phone   = ($data->phone !== null) ? Defuse\Crypto\Crypto::decrypt($data->phone, $secretKey) : null;
                $data->address = ($data->address !== null) ? Defuse\Crypto\Crypto::decrypt($data->address, $secretKey) : null;
            }
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \Exception
     *
     * @return  int   Item ID
     */
    public function save($data)
    {
        $id    = ArrayHelper::getValue($data, 'id', 0, 'int');
        $name  = ArrayHelper::getValue($data, 'name', '', 'string');
        $alias = ArrayHelper::getValue($data, 'alias', '', 'string');
        $bio   = ArrayHelper::getValue($data, 'bio', '', 'string');

        if (!$bio) {
            $bio = null;
        }

        // Prepare gender.
        $allowedGender = array('male', 'female');
        $gender        = trim(ArrayHelper::getValue($data, 'gender'));
        if (!in_array($gender, $allowedGender, true)) {
            $gender = 'male';
        }

        // Prepare birthday
        $birthday = SocialCommunityHelper::prepareBirthday($data);

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('name', $name);
        $row->set('alias', $alias);
        $row->set('bio', $bio);
        $row->set('birthday', $birthday);
        $row->set('gender', $gender);

        $this->prepareTable($row);
        $this->prepareImages($row, $data);
        $this->prepareContact($row, $data);

        $row->store(true);

        $id = $row->get('id');

        // Update the name in Joomla! users table
        SocialCommunityHelper::updateName($row->get('user_id'), $name);

        $this->saveSocialProfiles($row->get('user_id'), $data);

        return $id;
    }

    /**
     * Method to save social profiles data.
     *
     * @param    int   $userId
     * @param    array $data
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function saveSocialProfiles($userId, $data)
    {
        // Prepare profiles.
        $profiles = array(
            'facebook' => ArrayHelper::getValue($data, 'facebook'),
            'twitter'  => ArrayHelper::getValue($data, 'twitter'),
            'linkedin' => ArrayHelper::getValue($data, 'linkedin')
        );

        $allowedTypes = array('facebook', 'twitter', 'linkedin');

        foreach ($profiles as $key => $alias) {
            $type  = Joomla\String\StringHelper::trim($key);
            $alias = Joomla\String\StringHelper::trim($alias);

            if (!in_array($type, $allowedTypes, true)) {
                continue;
            }

            $keys = array(
                'user_id' => (int)$userId,
                'type'    => $type
            );

            // Load a record from the database
            $row = $this->getTable('SocialProfile');
            $row->load($keys);

            // Remove old
            if ($row->get('id') > 0 and !$alias) { // If there is a record but there is no alias, remove the record.
                $row->delete();
            } elseif (!$alias) { // If missing alias, continue to next social profile.
                continue;
            } else { // Add new

                if (!$row->get('id')) {
                    $row->set('user_id', (int)$userId);
                }

                $row->set('alias', $alias);
                $row->set('type', $type);

                $row->store();
            }
        }
    }
    
    protected function prepareTable($table)
    {
        // If an alias does not exist, I will generate the new one from the user name.
        if (!$table->get('alias')) {
            $table->set('alias', $table->get('name'));
        }

        $alias  = Socialcommunity\Profile\Helper::safeAlias($table->get('alias'), $table->get('user_id'));
        $table->set('alias', $alias);
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param SocialCommunityTableProfile $table
     * @param array                       $data
     *
     * @throws  \Exception
     * @since    1.6
     */
    protected function prepareImages($table, $data)
    {
        // Prepare image
        if (!empty($data['image'])) {
            $params              = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $filesystemHelper    = new Prism\Filesystem\Helper($params);

            $mediaFolder         = $filesystemHelper->getMediaFolder($data['user_id']);
            $storageFilesystem   = $filesystemHelper->getFilesystem();

            // Delete old image if I upload a new one
            if ($table->get('image')) {
                $this->deleteImages($table, $storageFilesystem, $mediaFolder);
            }

            // Move the images from temporary to media folder.
            $this->moveImages($data, $storageFilesystem, $mediaFolder);
            
            $table->set('image', $data['image']);
            $table->set('image_small', $data['image_small']);
            $table->set('image_icon', $data['image_icon']);
            $table->set('image_square', $data['image_square']);
        }
    }

    /**
     * Move the images from temporary to media folder.
     *
     * @param array $data
     * @param League\Flysystem\Filesystem $storageFilesystem
     * @param string $mediaFolder
     *
     * @throws Exception
     */
    protected function moveImages($data, $storageFilesystem, $mediaFolder)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */
        
        $temporaryFolder = JPath::clean($app->get('tmp_path'), '/');
        
        // Move the files to the media folder.
        $temporaryAdapter    = new League\Flysystem\Adapter\Local($temporaryFolder);
        $temporaryFilesystem = new League\Flysystem\Filesystem($temporaryAdapter);

        $manager = new League\Flysystem\MountManager([
            'temporary' => $temporaryFilesystem,
            'storage'   => $storageFilesystem
        ]);

        $manager->move('temporary://'.$data['image'], 'storage://'. $mediaFolder .'/'. $data['image']);
        $manager->move('temporary://'.$data['image_small'], 'storage://'. $mediaFolder .'/'. $data['image_small']);
        $manager->move('temporary://'.$data['image_icon'], 'storage://'. $mediaFolder .'/'. $data['image_icon']);
        $manager->move('temporary://'.$data['image_square'], 'storage://'. $mediaFolder .'/'. $data['image_square']);
    }
    
    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param SocialCommunityTableProfile $table
     * @param array                       $data
     *
     * @throws \Exception
     * @since    1.6
     */
    protected function prepareContact($table, $data)
    {
        $secretKey  = JFactory::getApplication()->get('secret');

        if (!$data['phone']) {
            $data['phone'] = null;
        } else {
            $data['phone'] = Defuse\Crypto\Crypto::encrypt($data['phone'], $secretKey);
        }

        if (!$data['address']) {
            $data['address'] = null;
        } else {
            $data['address'] = Defuse\Crypto\Crypto::encrypt($data['address'], $secretKey);
        }

        if (!$data['website']) {
            $data['website'] = null;
        }

        $table->set('phone', $data['phone']);
        $table->set('address', $data['address']);
        $table->set('country_id', (int)$data['country_id']);
        $table->set('location_id', (int)$data['location_id']);
        $table->set('website', $data['website']);
    }

    /**
     * Upload an image
     *
     * @param array $uploadedFileData Array with information about uploaded file.
     *
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \Exception
     *
     * @return array
     */
    public function uploadImage(array $uploadedFileData)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $uploadedFile = ArrayHelper::getValue($uploadedFileData, 'tmp_name');
        $uploadedName = ArrayHelper::getValue($uploadedFileData, 'name');
        $errorCode    = ArrayHelper::getValue($uploadedFileData, 'error');

        $params          = JComponentHelper::getParams('com_socialcommunity');
        /** @var  $params Joomla\Registry\Registry */

        $temporaryFolder = JPath::clean($app->get('tmp_path'), '/');

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams   = JComponentHelper::getParams('com_media');

        // Prepare size validator.
        $KB            = pow(1024, 2);
        $fileSize      = ArrayHelper::getValue($uploadedFileData, 'size', 0, 'int');
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

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Upload the file.
        $rootFolder      = JPath::clean($app->get('tmp_path'), '/');
        $filesystemLocal = new Prism\Filesystem\Adapter\Local($rootFolder);
        $sourceFile      = $filesystemLocal->upload($uploadedFileData);

        if (!$sourceFile) {
            throw new \RuntimeException(\JText::_('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Generate file names for the image files.
        $generatedName = Prism\Utilities\StringHelper::generateRandomString(16);

        // Resize image
        $image = new Prism\File\Image($sourceFile);

        $resizingOptions = new Joomla\Registry\Registry();
        $resizingOptions->set('create_new', Prism\Constants::NO);
        $resizingOptions->set('filename', $generatedName);
        $resizingOptions->set('quality', $params->get('image_quality', Prism\Constants::QUALITY_VERY_HIGH));

        // Create profile picture
        $resizingOptions->set('width', $params->get('image_width', 200));
        $resizingOptions->set('height', $params->get('image_height', 200));
        $resizingOptions->set('suffix', '_image');
        $fileData  = $image->resize($temporaryFolder, $resizingOptions);
        $imageName = $fileData['filename'];

        // Create small profile picture
        $resizingOptions->set('width', $params->get('small_width', 100));
        $resizingOptions->set('height', $params->get('small_height', 100));
        $resizingOptions->set('suffix', '_small');
        $fileData  = $image->resize($temporaryFolder, $resizingOptions);
        $smallName = $fileData['filename'];

        // Create square picture
        $resizingOptions->set('width', $params->get('square_width', 50));
        $resizingOptions->set('height', $params->get('square_height', 50));
        $resizingOptions->set('suffix', '_square');
        $fileData   = $image->resize($temporaryFolder, $resizingOptions);
        $squareName = $fileData['filename'];

        // Create icon picture
        $resizingOptions->set('width', $params->get('icon_width', 24));
        $resizingOptions->set('height', $params->get('icon_height', 24));
        $resizingOptions->set('suffix', '_icon');
        $fileData   = $image->resize($temporaryFolder, $resizingOptions);
        $iconName = $fileData['filename'];

        // Remove the temporary file
        if (JFile::exists($sourceFile)) {
            JFile::delete($sourceFile);
        }

        return array(
            'image'        => $imageName,
            'image_small'  => $smallName,
            'image_icon'   => $iconName,
            'image_square' => $squareName
        );
    }

    /**
     * Delete image only
     *
     * @param int $id Item ID.
     * @param League\Flysystem\Filesystem  $filesystem
     * @param string $mediaFolder
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \Exception
     */
    public function removeImage($id, $filesystem, $mediaFolder)
    {
        // Load category data
        $row = $this->getTable();
        $row->load($id);

        if ((int)$row->get('id') > 0) {
            $this->deleteImages($row, $filesystem, $mediaFolder);

            $row->set('image', '');
            $row->set('image_small', '');
            $row->set('image_square', '');
            $row->set('image_icon', '');
            $row->store();
        }
    }

    /**
     * Delete the images.
     *
     * @param JTable $row
     * @param League\Flysystem\Filesystem  $filesystem
     * @param string $mediaFolder
     *
     * @throws \Exception
     */
    protected function deleteImages($row, $filesystem, $mediaFolder)
    {
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
    }
}
