<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use League\Flysystem\Filesystem;
use Socialcommunity\Profile\Profile;
use Socialcommunity\Value\Profile\Image as ProfileImage;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

// no direct access
defined('_JEXEC') or die;

// Register Observers
JLoader::register('SocialcommunityObserverProfile', SOCIALCOMMUNITY_PATH_COMPONENT_ADMINISTRATOR . '/tables/observers/profile.php');
JObserverMapper::addObserverClassToClass('SocialcommunityObserverProfile', 'SocialcommunityTableProfile', array('typeAlias' => 'com_socialcommunity.profile'));

/**
 * This model provides functionality for managing user profile.
 *
 * @package      Socialcommunity
 * @subpackage   Components
 */
class SocialcommunityModelProfile extends JModelAdmin
{
    protected $item = array();

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  SocialcommunityTableProfile|bool
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialcommunityTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Stock method to auto-populate the model state.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState()
    {
        // Get the pk of the record from the request.
        $value = JFactory::getApplication()->input->getUint('id');
        $this->setState($this->getName() . '.id', $value);

        // Load the parameters.
        $value = JComponentHelper::getParams($this->option);
        $this->setState('params', $value);
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
     * @return  stdClass|null The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Check the session for previously entered form data.
        $data = $app->getUserState($this->option . '.edit.profile.data', array());

        if (!$data) {
            $itemId = $app->input->get->getUint('id');
            $data   = $this->getItem($itemId);

            if ($data and (int)$data->id > 0) {
                $userId = (int)$data->user_id;
                
                // Prepare locations
                if ($data->location_id > 0) {
                    $mapper     = new Socialcommunity\Location\Mapper(new Socialcommunity\Location\Gateway\JoomlaGateway(JFactory::getDbo()));
                    $repository = new Socialcommunity\Location\Repository($mapper);
                    $location   = $repository->fetchById($data->location_id);

                    $locationName = $location->getName();
                    if ($location->getCountryCode()) {
                        $locationName .= ', ' . $location->getCountryCode();
                    }

                    if ($locationName !== '') {
                        $data->location_preview = $locationName;
                    }
                }

                // Decrypt phone and address.
                if (count($data) > 0 and $data->secret_key) {
                    $password = $app->get('secret') . $userId;
                    $cryptor  = new \Socialcommunity\Profile\Contact\Cryptor($data->secret_key, $password);

                    $contact = new \Socialcommunity\Profile\Contact\Contact();
                    $contact->setPhone($data->phone);
                    $contact->setAddress($data->address);

                    $contact = $cryptor->decrypt($contact);

                    $data->phone   = $contact->getPhone();
                    $data->address = $contact->getAddress();
                }
            }
        }

        return $data;
    }

    /**
     * Method to get an object.
     *
     * @param    int $pk The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     *
     * @throws \RuntimeException
     */
    public function getItem($pk = null)
    {
        $pk = $pk ?: (int)$this->getState($this->getName() . '.id');

        if (!array_key_exists($pk, $this->item)) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select(
                    'a.id, a.user_id, a.name, a.alias, a.image, a.bio, a.birthday, a.gender, a.location_id, a.country_code, a.website, ' .
                    'b.phone, b.address, b.secret_key'
                )
                ->from($db->quoteName('#__itpsc_profiles', 'a'))
                ->leftJoin($db->quoteName('#__itpsc_profilecontacts', 'b') . ' ON a.user_id = b.user_id')
                ->where('a.id = ' . (int)$pk);

            $db->setQuery($query, 0, 1);

            $this->item[$pk] = $db->loadObject();
        }

        return $this->item[$pk];
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
        $id   = ArrayHelper::getValue($data, 'id', 0, 'int');
        $name = ArrayHelper::getValue($data, 'name', '', 'string');

        $alias = ArrayHelper::getValue($data, 'alias', '', 'string');
        $alias = Prism\Utilities\StringHelper::stringUrlSafe($alias);

        $bio = ArrayHelper::getValue($data, 'bio', '', 'string');
        $bio = $bio ?: null;

        $website = ArrayHelper::getValue($data, 'website', '', 'string');
        $website = $website ?: null;

        // Prepare gender.
        $gender = trim(ArrayHelper::getValue($data, 'gender'));
        if (!in_array($gender, ['male', 'female'], true)) {
            $gender = 'male';
        }

        // Prepare birthday
        $birthday = \Socialcommunity\Profile\Helper\Helper::prepareBirthday($data['birthday']);

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('name', $name);
        $row->set('alias', $alias);
        $row->set('bio', $bio);
        $row->set('gender', $gender);
        $row->set('birthday', $birthday);
        $row->set('country_code', $data['country_code']);
        $row->set('location_id', (int)$data['location_id']);
        $row->set('website', $website);

        $this->prepareTable($row);
        $this->prepareImages($row, $data);

        $row->store(true);

        $id = $row->get('id');

        $this->storeContact($row->get('user_id'), $data);
        $this->updateUserName($row->get('user_id'), $name);

        return $id;
    }

    protected function prepareTable($table)
    {
        // If the alias does not exist, I will generate new one from the user's name.
        if (!$table->get('alias')) {
            $table->set('alias', Prism\Utilities\StringHelper::stringUrlSafe($table->get('name')));
        }

        // Check if alias exists in the database.
        $validatorAlias = new \Socialcommunity\Validator\Profile\Alias($table->get('alias'), $table->get('user_id'));
        $validatorAlias->setGateway(new \Socialcommunity\Validator\Profile\Gateway\Joomla\Alias(JFactory::getDbo()));

        // Generate new alias if the new one already exists.
        if (!$validatorAlias->isValid()) {
            $alias = $table->get('alias') . '-' . Prism\Utilities\StringHelper::generateRandomString(5);
            $table->set('alias', $alias);
        }
    }

    /**
     * Update the user's name in Joomla __users table.
     *
     * @param int    $userId
     * @param string $name
     */
    protected function updateUserName($userId, $name)
    {
        // Store basic profile data.
        $basicRequest = new \Socialcommunity\Profile\Command\Request\Basic();
        $basicRequest
            ->setUserId($userId)
            ->setName($name);

        // Update user account name.
        $gateway = new \Socialcommunity\Account\Command\Gateway\Joomla\UpdateName(JFactory::getDbo());
        $command = new \Socialcommunity\Account\Command\UpdateName($basicRequest);
        $command->setGateway($gateway);
        $command->handle();
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param SocialcommunityTableProfile $table
     * @param array                       $data
     *
     * @throws  \Exception
     * @since    1.6
     */
    protected function prepareImages($table, $data)
    {
        // Prepare image
        if (!empty($data['image'])) {
            $params = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $filesystemHelper  = new Prism\Filesystem\Helper($params);
            $mediaFolder       = $filesystemHelper->getMediaFolder($data['user_id']);
            $storageFilesystem = $filesystemHelper->getFilesystem();

            // Delete old image if I upload a new one
            if ($table->get('image')) {
                $profileImage               = new ProfileImage;
                $profileImage->image        = $table->get('image');
                $profileImage->image_icon   = $table->get('image_icon');
                $profileImage->image_small  = $table->get('image_small');
                $profileImage->image_square = $table->get('image_square');

                $this->deleteImage($profileImage, $storageFilesystem, $mediaFolder);
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
     * @param array      $data
     * @param Filesystem $storageFilesystem
     * @param string     $mediaFolder
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

        $manager->move('temporary://' . $data['image'], 'storage://' . $mediaFolder . '/' . $data['image']);
        $manager->move('temporary://' . $data['image_small'], 'storage://' . $mediaFolder . '/' . $data['image_small']);
        $manager->move('temporary://' . $data['image_icon'], 'storage://' . $mediaFolder . '/' . $data['image_icon']);
        $manager->move('temporary://' . $data['image_square'], 'storage://' . $mediaFolder . '/' . $data['image_square']);
    }

    /**
     * Encrypt and store user's contact data.
     *
     * @param int   $userId
     * @param array $data
     *
     * @throws \Exception
     * @since    1.6
     */
    protected function storeContact($userId, $data)
    {
        $app = \JFactory::getApplication();

        // Prepare conditions.
        $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator' => '=', 'table' => 'a']);
        $conditions      = new Conditions();
        $conditions->addCondition($conditionUserId);

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        $gateway    = new \Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
        $repository = new \Socialcommunity\Profile\Contact\Repository($gateway);

        // Fetch the contact data.
        $contact = $repository->fetch($databaseRequest);
        if (!$contact->getId()) {
            $contact->setUserId($userId);
        }

        $contact->setAddress($data['address']);
        $contact->setPhone($data['phone']);

        // Generate new secret key.
        $password  = $app->get('secret') . $userId;
        $key       = \Defuse\Crypto\KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
        $secretKey = $key->saveToAsciiSafeString();

        $cryptor = new \Socialcommunity\Profile\Contact\Cryptor($secretKey, $password);
        $contact = $cryptor->encrypt($contact);

        $contact->setSecretKey($secretKey);
        $repository->store($contact);
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

        $params = JComponentHelper::getParams('com_socialcommunity');
        /** @var  $params Joomla\Registry\Registry */

        $temporaryFolder = JPath::clean($app->get('tmp_path'), '/');

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams = JComponentHelper::getParams('com_media');

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
        $fileData = $image->resize($temporaryFolder, $resizingOptions);
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
     * Delete the images.
     *
     * @param ProfileImage $profileImage
     * @param Filesystem   $filesystem
     * @param string       $mediaFolder
     *
     * @throws \Exception
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
     * Delete the profile picture.
     *
     * @param Profile    $profile
     * @param Filesystem $filesystem
     * @param string     $mediaFolder
     *
     * @throws Exception
     */
    public function removeImage(Profile $profile, Filesystem $filesystem, $mediaFolder)
    {
        $profileImage               = new ProfileImage;
        $profileImage->image        = $profile->getImage();
        $profileImage->image_icon   = $profile->getImageIcon();
        $profileImage->image_small  = $profile->getImageSmall();
        $profileImage->image_square = $profile->getImageSquare();

        $this->deleteImage($profileImage, $filesystem, $mediaFolder);

        // Initialize the value object of the profile image.
        $profileImage             = new ProfileImage;
        $profileImage->profile_id = $profile->getId();

        $commandUpdateImage = new \Socialcommunity\Profile\Command\UpdateImage($profileImage);
        $commandUpdateImage->setGateway(new \Socialcommunity\Profile\Command\Gateway\Joomla\UpdateImage(JFactory::getDbo()));
        $commandUpdateImage->handle();
    }
}
