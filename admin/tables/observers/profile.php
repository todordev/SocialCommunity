<?php
/**
 * @package      Socialcommunity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package      Socialcommunity
 * @subpackage   Component
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class SocialcommunityObserverProfile extends JTableObserver
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
     * @throws \InvalidArgumentException
     * @return  SocialcommunityObserverProfile
     *
     * @since   3.1.2
     */
    public static function createObserver(JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);

        $observer->typeAliasPattern = ArrayHelper::getValue($params, 'typeAlias');

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
     * @throws  \UnexpectedValueException
     * @throws  \InvalidArgumentException
     * @throws  \RuntimeException
     */
    public function onBeforeDelete($pk)
    {
        $db = $this->table->getDbo();

        $condition  = new \Prism\Database\Condition\Condition(['column' => 'user_id', 'operator' => '=', 'value' => (int)$this->table->get('user_id'), 'table' => 'a']);
        $conditions = new \Prism\Database\Condition\Conditions;
        $conditions->addCondition($condition);

        $databaseRequest = new \Prism\Database\Request\Request();
        $databaseRequest->setConditions($conditions);

        $mapper     = new \Socialcommunity\Profile\Mapper(new \Socialcommunity\Profile\Gateway\JoomlaGateway($db));
        $repository = new \Socialcommunity\Profile\Repository($mapper);

        $profile = $repository->fetch($databaseRequest);
        if ($profile->getId()) {
            $params = JComponentHelper::getParams('com_socialcommunity');
            /** @var  $params Joomla\Registry\Registry */

            $filesystemHelper = new Prism\Filesystem\Helper($params);
            $mediaFolder      = $filesystemHelper->getMediaFolder($profile->getUserId());
            $filesystem       = $filesystemHelper->getFilesystem();

            $profileImage               = new Socialcommunity\Value\Profile\Image;
            $profileImage->image        = $profile->getImage();
            $profileImage->image_icon   = $profile->getImageIcon();
            $profileImage->image_small  = $profile->getImageSmall();
            $profileImage->image_square = $profile->getImageSquare();

            $deleteImageCommand = new \Socialcommunity\Profile\Command\DeleteImage($profileImage, $filesystem, $mediaFolder);
            $deleteImageCommand->handle();
        }
    }
}
