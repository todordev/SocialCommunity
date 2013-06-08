<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class SocialCommunityModelForm extends JModelAdmin {
    
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
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        
        $app = JFactory::getApplication("Site");
        /** @var $app JSite **/
        
        // Get the primary key of the record 
		$value = JFactory::getUser()->id;
		$this->setState($this->option.'.profile.user_id', $value);

		// Load the parameters.
        $params	= $app->getParams($this->option);
		$this->setState('params', $params);
		
    }
    
    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm($this->option.'.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		$data	    = $app->getUserState($this->option.'.edit.profile.data', array());
		if(!$data) {
		    $data   = $this->getItem();
		}

		return $data;
    }
    
	/**
	 * Method to get an object.
	 *
	 * @param	integer	The id of the object to get.
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
	    if (!$id) {
	        $id = $this->getState($this->option.'.profile.user_id');
		}
		
		$item = null;
		
		if (!empty($id)) {

		    $db     = JFactory::getDbo();
		    $query  = $db->getQuery(true);
		    $query
		        ->select("*")
		        ->from("#__itpsc_profiles")
		        ->where("id = " . (int)$id);

		    $db->setQuery($query, 0, 1);
		    $item = $db->loadAssoc();
            
			// Check published state.
			if (empty($item)){
				return null;
			}

		}

		return $item;
	}
    
    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The record id on success, null on failure.
     * @since	1.6
     */
    public function save($data) {
        
        $id        = JFactory::getUser()->get("id");
        $name      = JArrayHelper::getValue($data, "name");
        $bio       = JArrayHelper::getValue($data, "bio");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("name",    $name);
        $row->set("bio",     $bio);
        
        $this->prepareTable($row, $data);
        
        $row->store();
        
        return $row->id;
        
    }

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table, $data) {
	    
	    // Prepare image
        if(!empty($data["image"])){
            
            // Delete old image if I upload a new one
            if(!empty($table->image)){
                jimport('joomla.filesystem.file');
                
                $params       = JComponentHelper::getParams($this->option);
		        $imagesFolder = $params->get("images_directory", "images/profiles");
		    
                // Remove an image from the filesystem
                $fileImage  = $imagesFolder .DIRECTORY_SEPARATOR. $table->image;
                $fileSmall  = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_small;
                $fileIcon   = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_icon;
                $fileSquare = $imagesFolder .DIRECTORY_SEPARATOR. $table->image_square;
               
                if(is_file($fileImage)) {
                    JFile::delete($fileImage);
                }
                
                if(is_file($fileSmall)) {
                    JFile::delete($fileSmall);
                }
                
                if(is_file($fileIcon)) {
                    JFile::delete($fileIcon);
                }
                
                if(is_file($fileSquare)) {
                    JFile::delete($fileSquare);
                }
            
            }
            $table->set("image",        $data["image"]);
            $table->set("image_small",  $data["image_small"]);
            $table->set("image_icon",   $data["image_icon"]);
            $table->set("image_square", $data["image_square"]);
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
        
        $uploadedFile  = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName  = JArrayHelper::getValue($image, 'name');
        
        // Load parameters.
        $params        = JComponentHelper::getParams($this->option);
        $destFolder    = $params->get("images_directory", "images/profiles");
        
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
        
        $tmpFolder            = $app->getCfg("tmp_path");
        
        // Joomla! media extension parameters
        $mediaParams     = JComponentHelper::getParams("com_media");
        
        $upload          = new ITPrismFileUploadImage($image);
        
        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $upload->setMimeTypes($mimeTypes);
        
        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $upload->setImageExtensions($imageExtensions);
        
        $uploadMaxSize   = $mediaParams->get("upload_maxsize");
        $KB              = 1024 * 1024;
        $upload->setMaxFileSize( round($uploadMaxSize * $KB, 0) );
        
        // Validate the file
        $upload->validate();
        
        // Generate temporary file name
        $seed  = substr(md5(uniqid(time() * rand(), true)), 0, 10);

        $ext   = JFile::makeSafe(JFile::getExt($image['name']));
        
        $tmpImageName  = JString::substr(JApplication::getHash($seed), 0, 32).".".$ext;
        $destination   = $tmpFolder.DIRECTORY_SEPARATOR.$tmpImageName;
        
        // Upload temporary file
        $upload->upload($destination);
        
        if(!is_file($destination)){
            throw new Exception('COM_SOCIALCOMMUNITY_ERROR_FILE_CANT_BE_UPLOADED');
        }
        
        // Generate filenames for the image files
        
        $generatedName = JString::substr(JApplication::getHash($seed), 0, 32);
        $imageName     = $generatedName . "_image.png";
        $smallName     = $generatedName . "_small.png";
        $squareName    = $generatedName . "_square.png";
        $iconName      = $generatedName . "_icon.png";
            
        // Resize image
        $image = new JImage();
        $image->loadFile($destination);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_SOCIALCOMMUNITY_ERROR_FILE_NOT_FOUND', $destination));
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
        if(is_file($destination)){
            JFile::delete($destination);
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
		    $imagesFolder = $params->get("images_directory", "images/profiles");
		    
            // Remove an image from the filesystem
            $fileImage  = $imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            $fileSmall  = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_small;
            $fileSquare = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_square;
            $fileIcon   = $imagesFolder.DIRECTORY_SEPARATOR.$row->image_icon;

            if(is_file($fileImage)) {
                JFile::delete($fileImage);
            }
            
            if(is_file($fileSmall)) {
                JFile::delete($fileSmall);
            }
            
            if(is_file($fileSquare)) {
                JFile::delete($fileSquare);
            }
            
            if(is_file($fileIcon)) {
                JFile::delete($fileIcon);
            }
        }
        
        $row->set("image", "");
        $row->set("image_small", "");
        $row->set("image_icon", "");
        $row->set("image_square", "");
        $row->store();
    
    }
    
}