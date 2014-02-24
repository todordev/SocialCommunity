<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * This model provides functionality for managing user profile.
 * 
 * @package      SocialCommunity
 * @subpackage   Components
 */
class SocialCommunityModelProfile extends JModelAdmin {
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Profile', $prefix = 'SocialCommunityTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        
        // Get the form.
        $form = $this->loadForm($this->option.'.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
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
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.profile.data', array());
        
        if(empty($data)){
            $data    = $this->getItem();
            
            
            if(!empty($data->id)) {
                
                $socialProfiles   = $this->getSocialProfiles($data->id);
                
                if(!empty($socialProfiles)) {
                    foreach($socialProfiles as $item) {
                        $data->$item["type"] = $item["alias"];
                    }
                }
                
            }
            
        }
        
        return $data;
    }
    
    /**
     * Save data into the DB
     * 
     * @param $data   The data about item
     * 
     * @return     Item ID
     */
    public function save($data){
        
        $id     = JArrayHelper::getValue($data, "id");
        $name   = JArrayHelper::getValue($data, "name");
        $alias  = JArrayHelper::getValue($data, "alias");
        $bio    = JArrayHelper::getValue($data, "bio");
        if(empty($bio)) { $bio = null; }
        
        // Prepare gender.
        $allowedGender = array("male", "female");
        $gender        = JString::trim(JArrayHelper::getValue($data, "gender"));
        if(!in_array($gender, $allowedGender)) {
            $gender = "male";
        }
        
        // Prepare birthday
        $birthday = $this->prepareBirthday($data);
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("name",       $name);
        $row->set("alias",      $alias);
        $row->set("bio",        $bio);
        $row->set("birthday",   $birthday);
        $row->set("gender",     $gender);
        
        $this->prepareTable($row, $data);
        
        $id = $row->store(true);
        
        // Update the name in Joomla! users table
        $this->updateName($id, $name);
        
        $this->saveSocialProfiles($row->id, $data);
        
        return $row->id;
    }
    
    /**
     * Method to save social profiles data.
     *
     * @param	array		The form data.
     * @return	mixed		The record id on success, null on failure.
     * @since	1.6
     */
    public function saveSocialProfiles($userId, $data) {
    
        // Prepare profiles.
        $profiles = array(
            "facebook" => JArrayHelper::getValue($data, "facebook"),
            "twitter"  => JArrayHelper::getValue($data, "twitter"),
            "linkedin" => JArrayHelper::getValue($data, "linkedin")
        );
        
        $allowedTypes = array("facebook", "twitter", "linkedin");
    
        foreach($profiles as $key => $alias) {
    
            $type      = JString::trim($key);
            $alias     = JString::trim($alias);
    
            if(!in_array($type, $allowedTypes)) {
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
            if(!empty($row->id) AND !$alias) { // If there is a record but there is now alias, remove old record.
                
                $row->delete();
                
            } else if(!$alias) { // If missing alias, continue to next social profile.

                continue;
                
            } else { // Add new
            
                if(!$row->id) {
                    $row->set("user_id", (int)$userId);
                }
        
                $row->set("alias",    $alias);
                $row->set("type",     $type);
        
                $row->store();
            
            }
            
        }
    
    }
    
    protected function prepareBirthday($data) {
        
        $birthdayDay   = JString::trim(JArrayHelper::getValue($data["birthday"], "day"));
        $birthdayMonth = JString::trim(JArrayHelper::getValue($data["birthday"], "month"));
        $birthdayYear  = JString::trim(JArrayHelper::getValue($data["birthday"], "year"));
        if(!$birthdayDay)   { $birthdayDay = "00"; }
        if(!$birthdayMonth) { $birthdayMonth = "00"; }
        if(!$birthdayYear)  { $birthdayYear = "0000"; }
        
        $birthday      = $birthdayYear."-".$birthdayMonth."-".$birthdayDay;
        
        jimport("socialcommunity.date");
        $date           = new SocialCommunityDate($birthday);
        if(!$date->isValid()) {
            $birthday = "0000-00-00";
        }
        
        return $birthday;
    }
    
    /**
     * Prepare and sanitise the table prior to saving.
     * @since	1.6
     */
    protected function prepareTable(&$table, $data) {
         
        // Prepare image
        if(!empty($data["image"])){
            
            // Delete old image if I upload a new one
            if(!empty($table->image)){
                
                jimport('joomla.filesystem.file');
                
                $params       = JComponentHelper::getParams($this->option);
		        $imagesFolder = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/profiles"));
		    
                // Remove an image from the filesystem
                $fileImage  = $imagesFolder .DIRECTORY_SEPARATOR. $table->image;
                $fileSmall  = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_small;
                $fileIcon   = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_icon;
                $fileSquare = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_square;
               
                if(JFile::exists($fileImage)) {
                    JFile::delete($fileImage);
                }
                
                if(JFile::exists($fileSmall)) {
                    JFile::delete($fileSmall);
                }
                
                if(JFile::exists($fileIcon)) {
                    JFile::delete($fileIcon);
                }
                
                if(JFile::exists($fileSquare)) {
                    JFile::delete($fileSquare);
                }
            
            }
            
            $table->set("image",        $data["image"]);
            $table->set("image_small",  $data["image_small"]);
            $table->set("image_icon",   $data["image_icon"]);
            $table->set("image_square", $data["image_square"]);
        }
        
        // Fix magic quotes
        if( get_magic_quotes_gpc() ) {
            $table->name    = stripcslashes($table->name);
            $table->bio     = stripcslashes($table->bio);
        }
        
        // If an alias does not exist, I will generate the new one from the user name.
        if(!$table->alias) {
            $table->alias = $table->name;
        }
        $table->alias = JApplication::stringURLSafe($table->alias);
    }
    
    /**
     * Upload an image
     *
     * @param array $image Array with information about uploaded file.
     */
    public function uploadImage($image) {
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        $names         = array("image", "small", "square");
    
        $uploadedFile  = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName  = JArrayHelper::getValue($image, 'name');
    
        $tmpFolder     = $app->getCfg("tmp_path");
    
        // Load parameters.
        $params          = JComponentHelper::getParams($this->option);
        $destFolder      = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/profiles"));
    
        $options = array(
                "width"           => $params->get("image_width"),
                "height"          => $params->get("image_height"),
                "small_width"     => $params->get("image_small_width"),
                "small_height"    => $params->get("image_small_height"),
                "square_width"    => $params->get("image_square_width"),
                "square_height"   => $params->get("image_square_height"),
                "icon_width"      => $params->get("image_icon_width"),
                "icon_height"     => $params->get("image_icon_height"),
        );
    
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
    
        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");
    
        $file           = new ITPrismFile();
    
        // Prepare size validator.
        $KB             = 1024 * 1024;
        $fileSize       = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
    
        $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
    
    
        // Prepare image validator.
        $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);
    
        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);
    
        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);
    
        $file
        ->addValidator($sizeValidator)
        ->addValidator($imageValidator);
    
        // Validate the file
        $file->validate();
    
        // Generate temporary file name
        $ext   = JFile::makeSafe(JFile::getExt($image['name']));
    
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(32);
    
        $tmpDestFile   = $tmpFolder.DIRECTORY_SEPARATOR.$generatedName.".".$ext;
    
        // Prepare uploader object.
        $uploader    = new ITPrismFileUploaderLocal($image);
        $uploader->setDestination($tmpDestFile);
    
        // Upload temporary file
        $file->setUploader($uploader);
    
        $file->upload();
    
        // Get file
        $tmpSourceFile = $file->getFile();
    
        if(!is_file($tmpSourceFile)){
            throw new RuntimeException('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED');
        }
    
        // Generate filenames for the image files.
        $generatedName->generateRandomString(32);
        $imageName     = $generatedName . "_image.png";
        $smallName     = $generatedName . "_small.png";
        $squareName    = $generatedName . "_square.png";
        $iconName      = $generatedName . "_icon.png";
    
        // Resize image
        $image = new JImage();
        $image->loadFile($tmpSourceFile);
        if (!$image->isLoaded()) {
            throw new RuntimeException(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND', $tmpSourceFile));
        }
    
        $imageFile   = $destFolder .DIRECTORY_SEPARATOR. $imageName;
        $smallFile   = $destFolder .DIRECTORY_SEPARATOR. $smallName;
        $squareFile  = $destFolder .DIRECTORY_SEPARATOR. $squareName;
        $iconFile    = $destFolder .DIRECTORY_SEPARATOR. $iconName;
    
        // Create profile picture
        $width       = JArrayHelper::getValue($options, "image_width",  200);
        $height      = JArrayHelper::getValue($options, "image_height", 200);
        $image->resize($width, $height, false);
        $image->toFile($imageFile, IMAGETYPE_PNG);
    
        // Create small profile picture
        $width       = JArrayHelper::getValue($options, "small_width",  100);
        $height      = JArrayHelper::getValue($options, "small_height", 100);
        $image->resize($width, $height, false);
        $image->toFile($smallFile, IMAGETYPE_PNG);
    
        // Create square picture
        $width       = JArrayHelper::getValue($options, "square_width",  50);
        $height      = JArrayHelper::getValue($options, "square_height", 50);
        $image->resize($width, $height, false);
        $image->toFile($squareFile, IMAGETYPE_PNG);
    
        // Create icon picture
        $width       = JArrayHelper::getValue($options, "icon_width",  24);
        $height      = JArrayHelper::getValue($options, "icon_height", 24);
        $image->resize($width, $height, false);
        $image->toFile($iconFile, IMAGETYPE_PNG);
    
        // Remove the temporary file
        if(JFile::exists($tmpSourceFile)){
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
     * @param integer Item id
     */
    public function removeImage($id){
    
        // Load category data
        $row = $this->getTable();
        $row->set("id", $id);
        $row->load();
    
        // Delete old image if I upload the new one
        if(!empty($row->image)){
            jimport('joomla.filesystem.file');
    
            $params       = JComponentHelper::getParams($this->option);
            $imagesFolder = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/profiles"));
    
            // Remove an image from the filesystem
            $fileImage  = $imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            $fileSmall  = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_small;
            $fileSquare = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_square;
            $fileIcon   = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_icon;
    
            if(JFile::exists($fileImage)) {
                JFile::delete($fileImage);
            }
    
            if(JFile::exists($fileSmall)) {
                JFile::delete($fileSmall);
            }
    
            if(JFile::exists($fileSquare)) {
                JFile::delete($fileSquare);
            }
    
            if(JFile::exists($fileIcon)) {
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
     * 
     * This method updates the name of user in  
     * the Joomla! users table.
     */
    protected function updateName($id, $name) {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query
            ->update($db->quoteName("#__users"))
            ->set($db->quoteName("name") ."=". $db->quote($name)) 
            ->where($db->quoteName("id") ."=". (int)$id) ;
            
        $db->setQuery($query);
        $db->execute();
    }
    
    /**
     * 
     * This method creats records in the table of profiles.
     * 
     * @param array $pks User IDs
     */
    public function create($pks) {
        
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        // Get data about user from table "users"
        $query
            ->select("a.id, a.name")
            ->from($db->quoteName("#__users") . " AS a")
            ->where("a.id IN (".implode(",", $pks).")");
            
        $db->setQuery($query);
        $results = $db->loadAssocList("id");
    
        // Preparing data for inserting
        $values = array();
        foreach($results as $result) {
            $values[] = $db->quote($result["id"]).','.$db->quote($result["name"]).','.$db->quote(JApplication::stringURLSafe($result["name"])) ;
        }
        
        $query = $db->getQuery(true);
        $query
            ->insert($db->quoteName("#__itpsc_profiles"))
            ->columns( $db->quoteName(array("id", "name", "alias")) )
            ->values($values);
        
        $db->setQuery($query);
        $db->execute();
        
    }
    
    /**
     * Verify and filter existing profiles 
     * and the new ones
     * 
     * @param  array $pks Primary Keys of users
     * @return array Return the keys of users without profiles. 
     */
    public function filterProfiles($pks) {
        
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Remove IDs what allready exists in the table of profiles
        $query
            ->select("a.id")
            ->from($db->quoteName("#__itpsc_profiles") . " AS a")
            ->where("a.id IN (".implode(",", $pks).")");
            
        $db->setQuery($query);
        $results = $db->loadColumn();
        
        $pks = array_diff($pks, $results);
        
        return $pks;
    }
    
    /**
     * Method to get an object.
     *
     * @param	integer	The id of the object to get.
     * @return	mixed	Object on success, false on failure.
     */
    public function getSocialProfiles($userId) {
         
        $items = array();
    
        if (!empty($userId)) {
    
            $db     = $this->getDbo();
            $query  = $db->getQuery(true);
            $query
                ->select("a.id, a.alias, a.type, a.user_id")
                ->from($db->quoteName("#__itpsc_socialprofiles", "a"))
                ->where("a.user_id = " . (int)$userId);
    
            $db->setQuery($query);
            $items = $db->loadAssocList();
    
            if (empty($items)){
                $items = array();
            }
    
        }
    
        return $items;
    }
}