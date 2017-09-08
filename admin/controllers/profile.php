<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

jimport('Prism.vendor.GuzzleHttp.init');
jimport('Prism.vendor.Aws.init');

/**
 * Socialcommunity profile controller class.
 *
 * @package      Socialcommunity
 * @subpackage   Components
 * @since        1.6
 */
class SocialcommunityControllerProfile extends Prism\Controller\Form\Backend
{
    /**
     * Proxy method that returns the model.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return SocialcommunityModelProfile
     */
    public function getModel($name = 'Profile', $prefix = 'SocialcommunityModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get form data
        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'id');

        $redirectOptions = array(
            'task' => $this->getTask(),
            'id'   => $itemId
        );

        $model = $this->getModel();
        /** @var $model SocialcommunityModelProfile */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate form data
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        try {
            $image = $this->input->files->get('jform', array(), 'array');
            $image = Joomla\Utilities\ArrayHelper::getValue($image, 'photo');

            // Upload image
            if (!empty($image['name'])) {
                $imageNames = $model->uploadImage($image);

                if (!empty($imageNames['image'])) {
                    $validData = array_merge($validData, $imageNames);
                }
            }

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectOptions);
    }

    /**
     * Delete the profile image.
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function removeImage()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->get->getUint('id');

        $redirectOptions = array(
            'view' => 'profile',
            'id'   => $itemId
        );

        $mapper     = new \Socialcommunity\Profile\Mapper(new \Socialcommunity\Profile\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new \Socialcommunity\Profile\Repository($mapper);

        $profile    = $repository->fetchById($itemId);
        if (!$profile->getId()) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_INVALID_PROFILE'), $redirectOptions);
            return;
        }

        try {
            $params = JComponentHelper::getParams('com_socialcommunity');

            $filesystemHelper    = new Prism\Filesystem\Helper($params);
            $storageFilesystem   = $filesystemHelper->getFilesystem();
            $mediaFolder         = $filesystemHelper->getMediaFolder($profile->getUserId());

            $model = $this->getModel();
            $model->removeImage($profile, $storageFilesystem, $mediaFolder);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_IMAGE_DELETED'), $redirectOptions);
    }
}
