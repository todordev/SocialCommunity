<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

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
     * @return  SocialCommunityTableProfile
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialCommunityTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.profile.data', array());

        if (empty($data)) {
            $data = $this->getItem();


            if (!empty($data->id)) {

                // Prepare social profiles
                $socialProfiles = $this->getSocialProfiles($data->id);
                if (!empty($socialProfiles)) {
                    foreach ($socialProfiles as $item) {
                        $data->$item["type"] = $item["alias"];
                    }
                }

                // Prepare locations
                if (!empty($data->location_id)) {
                    
                    $location = new SocialCommunity\Location(JFactory::getDbo());
                    $location->load($data->location_id);

                    $locationName = $location->getName(SocialCommunity\Constants::INCLUDE_COUNTRY_CODE);

                    if (!empty($locationName)) {
                        $data->location_preview = $locationName;
                    }

                }

            }

        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @return  int   Item ID
     */
    public function save($data)
    {
        $id    = Joomla\Utilities\ArrayHelper::getValue($data, "id");
        $name  = Joomla\Utilities\ArrayHelper::getValue($data, "name");
        $alias = Joomla\Utilities\ArrayHelper::getValue($data, "alias");
        $bio   = Joomla\Utilities\ArrayHelper::getValue($data, "bio");
        if (empty($bio)) {
            $bio = null;
        }

        // Prepare gender.
        $allowedGender = array("male", "female");
        $gender        = Joomla\String\String::trim(Joomla\Utilities\ArrayHelper::getValue($data, "gender"));
        if (!in_array($gender, $allowedGender)) {
            $gender = "male";
        }

        // Prepare birthday
        $birthday = $this->prepareBirthday($data);

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("name", $name);
        $row->set("alias", $alias);
        $row->set("bio", $bio);
        $row->set("birthday", $birthday);
        $row->set("gender", $gender);

        $this->prepareTable($row);
        $this->prepareImages($row, $data);
        $this->prepareContact($row, $data);

        $id = $row->store(true);

        // Update the name in Joomla! users table
        $this->updateName($id, $name);

        $this->saveSocialProfiles($row->get("id"), $data);

        return $row->get("id");
    }

    /**
     * Method to save social profiles data.
     *
     * @param    int   $userId
     * @param    array $data
     *
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function saveSocialProfiles($userId, $data)
    {
        // Prepare profiles.
        $profiles = array(
            "facebook" => Joomla\Utilities\ArrayHelper::getValue($data, "facebook"),
            "twitter"  => Joomla\Utilities\ArrayHelper::getValue($data, "twitter"),
            "linkedin" => Joomla\Utilities\ArrayHelper::getValue($data, "linkedin")
        );

        $allowedTypes = array("facebook", "twitter", "linkedin");

        foreach ($profiles as $key => $alias) {

            $type  = Joomla\String\String::trim($key);
            $alias = Joomla\String\String::trim($alias);

            if (!in_array($type, $allowedTypes)) {
                continue;
            }

            $keys = array(
                "user_id" => (int)$userId,
                "type"    => $type
            );

            // Load a record from the database
            $row = $this->getTable("SocialProfile");
            $row->load($keys);

            // Remove old
            if (!empty($row->id) and !$alias) { // If there is a record but there is now alias, remove old record.

                $row->delete();

            } elseif (!$alias) { // If missing alias, continue to next social profile.

                continue;

            } else { // Add new

                if (!$row->id) {
                    $row->set("user_id", (int)$userId);
                }

                $row->set("alias", $alias);
                $row->set("type", $type);

                $row->store();

            }
        }
    }

    protected function prepareBirthday($data)
    {
        $birthdayDay   = Joomla\String\String::trim(Joomla\Utilities\ArrayHelper::getValue($data["birthday"], "day"));
        $birthdayMonth = Joomla\String\String::trim(Joomla\Utilities\ArrayHelper::getValue($data["birthday"], "month"));
        $birthdayYear  = Joomla\String\String::trim(Joomla\Utilities\ArrayHelper::getValue($data["birthday"], "year"));
        if (!$birthdayDay) {
            $birthdayDay = "00";
        }
        if (!$birthdayMonth) {
            $birthdayMonth = "00";
        }
        if (!$birthdayYear) {
            $birthdayYear = "0000";
        }

        $birthday = $birthdayYear . "-" . $birthdayMonth . "-" . $birthdayDay;

        $date = new Prism\Validator\Date($birthday);
        if (!$date->isValid()) {
            $birthday = "0000-00-00";
        }

        return $birthday;
    }
    
    protected function prepareTable($table)
    {
        // Fix magic quotes
        if (get_magic_quotes_gpc()) {
            $table->set("name", stripcslashes($table->get("name")));
            $table->set("bio", stripcslashes($table->get("bio")));
        }

        // If an alias does not exist, I will generate the new one from the user name.
        if (!$table->get("alias")) {
            $table->set("alias", $table->get("name"));
        }
        $table->set("alias", JApplicationHelper::stringURLSafe($table->get("alias")));
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param SocialCommunityTableProfile $table
     * @param array                       $data
     *
     * @since    1.6
     */
    protected function prepareImages($table, $data)
    {
        // Prepare image
        if (!empty($data["image"])) {

            // Delete old image if I upload a new one
            if ($table->get("image")) {

                jimport('joomla.filesystem.file');

                /** @var  $params Joomla\Registry\Registry */
                $params       = JComponentHelper::getParams($this->option);
                $imagesFolder = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/profiles"));

                // Remove an image from the filesystem
                $fileImage  = $imagesFolder . DIRECTORY_SEPARATOR . $table->get("image");
                $fileSmall  = $imagesFolder . DIRECTORY_SEPARATOR . $table->get("image_small");
                $fileIcon   = $imagesFolder . DIRECTORY_SEPARATOR . $table->get("image_icon");
                $fileSquare = $imagesFolder . DIRECTORY_SEPARATOR . $table->get("image_square");

                if (JFile::exists($fileImage)) {
                    JFile::delete($fileImage);
                }

                if (JFile::exists($fileSmall)) {
                    JFile::delete($fileSmall);
                }

                if (JFile::exists($fileIcon)) {
                    JFile::delete($fileIcon);
                }

                if (JFile::exists($fileSquare)) {
                    JFile::delete($fileSquare);
                }

            }

            $table->set("image", $data["image"]);
            $table->set("image_small", $data["image_small"]);
            $table->set("image_icon", $data["image_icon"]);
            $table->set("image_square", $data["image_square"]);
        }

    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param SocialCommunityTableProfile $table
     * @param array                       $data
     *
     * @since    1.6
     */
    protected function prepareContact($table, $data)
    {
        if (!$data["phone"]) {
            $data["phone"] = null;
        }

        if (!$data["address"]) {
            $data["address"] = null;
        }

        if (!$data["website"]) {
            $data["website"] = null;
        }

        $table->set("phone", $data["phone"]);
        $table->set("address", $data["address"]);
        $table->set("country_id", (int)$data["country_id"]);
        $table->set("location_id", (int)$data["location_id"]);
        $table->set("website", $data["website"]);
    }

    /**
     * Upload an image
     *
     * @param array $image Array with information about uploaded file.
     *
     * @throws RuntimeException
     * @return array
     */
    public function uploadImage($image)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $uploadedFile = Joomla\Utilities\ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = Joomla\Utilities\ArrayHelper::getValue($image, 'name');
        $errorCode    = Joomla\Utilities\ArrayHelper::getValue($image, 'error');

        $tmpFolder = $app->get("tmp_path");

        /** @var  $params Joomla\Registry\Registry */
        $params     = JComponentHelper::getParams($this->option);
        $destFolder = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR. $params->get("images_directory", "/images/profiles"));

        $options = array(
            "width"         => $params->get("image_width"),
            "height"        => $params->get("image_height"),
            "small_width"   => $params->get("image_small_width"),
            "small_height"  => $params->get("image_small_height"),
            "square_width"  => $params->get("image_square_width"),
            "square_height" => $params->get("image_square_height"),
            "icon_width"    => $params->get("image_icon_width"),
            "icon_height"   => $params->get("image_icon_height"),
        );

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams = JComponentHelper::getParams("com_media");

        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
        jimport("itprism.file.validator.server");

        $file = new Prism\File\File();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

        // Prepare file size validator
        $sizeValidator = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode, array(UPLOAD_ERR_NO_FILE));

        // Prepare image validator.
        $imageValidator = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);

        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $ext = JFile::makeSafe(JFile::getExt($image['name']));

        $generatedName = new Prism\String();
        $generatedName->generateRandomString(32);

        $tmpDestFile = $tmpFolder . DIRECTORY_SEPARATOR . $generatedName . "." . $ext;

        // Prepare uploader object.
        $uploader = new Prism\File\Uploader\Local($uploadedFile);
        $uploader->setDestination($tmpDestFile);

        // Upload temporary file
        $file->setUploader($uploader);

        $file->upload();

        // Get file
        $tmpSourceFile = $file->getFile();

        if (!is_file($tmpSourceFile)) {
            throw new RuntimeException('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED');
        }

        // Generate file names for the image files.
        $generatedName->generateRandomString(32);
        $imageName  = $generatedName . "_image.png";
        $smallName  = $generatedName . "_small.png";
        $squareName = $generatedName . "_square.png";
        $iconName   = $generatedName . "_icon.png";

        // Resize image
        $image = new JImage();
        $image->loadFile($tmpSourceFile);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND', $tmpSourceFile));
        }

        $imageFile  = $destFolder . DIRECTORY_SEPARATOR . $imageName;
        $smallFile  = $destFolder . DIRECTORY_SEPARATOR . $smallName;
        $squareFile = $destFolder . DIRECTORY_SEPARATOR . $squareName;
        $iconFile   = $destFolder . DIRECTORY_SEPARATOR . $iconName;

        // Create profile picture
        $width  = Joomla\Utilities\ArrayHelper::getValue($options, "image_width", 200);
        $height = Joomla\Utilities\ArrayHelper::getValue($options, "image_height", 200);
        $image->resize($width, $height, false);
        $image->toFile($imageFile, IMAGETYPE_PNG);

        // Create small profile picture
        $width  = Joomla\Utilities\ArrayHelper::getValue($options, "small_width", 100);
        $height = Joomla\Utilities\ArrayHelper::getValue($options, "small_height", 100);
        $image->resize($width, $height, false);
        $image->toFile($smallFile, IMAGETYPE_PNG);

        // Create square picture
        $width  = Joomla\Utilities\ArrayHelper::getValue($options, "square_width", 50);
        $height = Joomla\Utilities\ArrayHelper::getValue($options, "square_height", 50);
        $image->resize($width, $height, false);
        $image->toFile($squareFile, IMAGETYPE_PNG);

        // Create icon picture
        $width  = Joomla\Utilities\ArrayHelper::getValue($options, "icon_width", 24);
        $height = Joomla\Utilities\ArrayHelper::getValue($options, "icon_height", 24);
        $image->resize($width, $height, false);
        $image->toFile($iconFile, IMAGETYPE_PNG);

        // Remove the temporary file
        if (JFile::exists($tmpSourceFile)) {
            JFile::delete($tmpSourceFile);
        }

        return $names = array(
            "image"        => $imageName,
            "image_small"  => $smallName,
            "image_icon"   => $iconName,
            "image_square" => $squareName
        );
    }

    /**
     * Delete image only
     *
     * @param integer $id Item id
     */
    public function removeImage($id)
    {
        // Load category data
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();

        // Delete old image if I upload the new one
        if ($row->get("image")) {
            jimport('joomla.filesystem.file');

            /** @var  $params Joomla\Registry\Registry */
            $params       = JComponentHelper::getParams($this->option);
            $imagesFolder = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/profiles"));

            // Remove an image from the filesystem
            $fileImage  = $imagesFolder . DIRECTORY_SEPARATOR . $row->get("image");
            $fileSmall  = $imagesFolder . DIRECTORY_SEPARATOR . $row->get("image_small");
            $fileSquare = $imagesFolder . DIRECTORY_SEPARATOR . $row->get("image_square");
            $fileIcon   = $imagesFolder . DIRECTORY_SEPARATOR . $row->get("image_icon");

            if (JFile::exists($fileImage)) {
                JFile::delete($fileImage);
            }

            if (JFile::exists($fileSmall)) {
                JFile::delete($fileSmall);
            }

            if (JFile::exists($fileSquare)) {
                JFile::delete($fileSquare);
            }

            if (JFile::exists($fileIcon)) {
                JFile::delete($fileIcon);
            }
        }

        $row->set("image", null);
        $row->set("image_small", null);
        $row->set("image_icon", null);
        $row->set("image_square", null);
        $row->store(true);
    }

    /**
     * This method updates the name of user in
     * the Joomla! users table.
     * 
     * @param int $id
     * @param string $name
     */
    protected function updateName($id, $name)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName("#__users"))
            ->set($db->quoteName("name") . "=" . $db->quote($name))
            ->where($db->quoteName("id") . "=" . (int)$id);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     *
     * This method creates records in the table of profiles.
     *
     * @param array $pks User IDs
     */
    public function create($pks)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Get data about user from table "users"
        $query
            ->select("a.id, a.name")
            ->from($db->quoteName("#__users", "a"))
            ->where("a.id IN (" . implode(",", $pks) . ")");

        $db->setQuery($query);
        $results = $db->loadAssocList("id");

        // Preparing data for inserting
        $values = array();
        foreach ($results as $result) {
            $values[] = $db->quote($result["id"]) . ',' . $db->quote($result["name"]) . ',' . $db->quote(JApplicationHelper::stringURLSafe($result["name"]));
        }

        $query = $db->getQuery(true);
        $query
            ->insert($db->quoteName("#__itpsc_profiles"))
            ->columns($db->quoteName(array("id", "name", "alias")))
            ->values($values);

        $db->setQuery($query);
        $db->execute();

    }

    /**
     * Verify and filter existing profiles
     * and the new ones
     *
     * @param  array $pks Primary Keys of users
     *
     * @return array Return the keys of users without profiles.
     */
    public function filterProfiles($pks)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Remove IDs what allready exists in the table of profiles
        $query
            ->select("a.id")
            ->from($db->quoteName("#__itpsc_profiles", "a"))
            ->where("a.id IN (" . implode(",", $pks) . ")");

        $db->setQuery($query);
        $results = $db->loadColumn();

        $pks = array_diff($pks, $results);

        return $pks;
    }

    /**
     * Method to get an object.
     *
     * @param    integer $userId The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getSocialProfiles($userId)
    {
        $items = array();

        if (!empty($userId)) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select("a.id, a.alias, a.type, a.user_id")
                ->from($db->quoteName("#__itpsc_socialprofiles", "a"))
                ->where("a.user_id = " . (int)$userId);

            $db->setQuery($query);
            $items = $db->loadAssocList();

            if (empty($items)) {
                $items = array();
            }
        }

        return $items;
    }
}
