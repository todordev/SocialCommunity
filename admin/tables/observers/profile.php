<?php
/**
 * @package      SocialCommunity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package      SocialCommunity
 * @subpackage   Component
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class SocialCommunityObserverProfile extends JTableObserver
{
    /**
     * The pattern for this table's TypeAlias
     *
     * @var    string
     * @since  3.1.2
     */
    protected $typeAliasPattern;

    /**
     * Creates the associated observer instance and attaches it to the $observableObject
     * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
     *
     * @param   JObservableInterface $observableObject The subject object to be observed
     * @param   array                $params           ( 'typeAlias' => $typeAlias )
     *
     * @return  SocialCommunityObserverProfile
     *
     * @since   3.1.2
     */
    public static function createObserver(JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);

        $observer->typeAliasPattern = Joomla\Utilities\ArrayHelper::getValue($params, 'typeAlias');

        return $observer;
    }

    /**
     * Pre-processor for $table->delete($pk)
     *
     * @param   mixed $pk An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @return  void
     *
     * @since   3.1.2
     * @throws  UnexpectedValueException
     */
    public function onBeforeDelete($pk)
    {
        $db = $this->table->getDbo();

        jimport('Socialcommunity.profile');

        $profile = new Socialcommunity\Profile\Profile($db);
        $profile->load($this->table->id);

        if ($profile->getId()) {

            $params       = JComponentHelper::getParams('com_socialcommunity');
            /** @var  $params Joomla\Registry\Registry */

            $filesystemHelper = new Prism\Filesystem\Helper($params);
            $mediaFolder = $filesystemHelper->getMediaFolderUri($profile->getId());
            $filesystem  = $filesystemHelper->getFilesystem();

            $profile->removeImages($filesystem, $mediaFolder);

        }
    }
}
