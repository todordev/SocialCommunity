<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\String\StringHelper as JStringHelper;
use Joomla\Utilities\ArrayHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Form controller class.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerBasic extends Prism\Controller\Form\Frontend
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        $data            = $this->input->post->get('jform', array(), 'array');
        $redirectOptions = array(
            'view' => 'form'
        );

        $model = $this->getModel();
        /** @var $model SocialcommunityModelBasic */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_FORM_LOADING'));
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        // Prepare birthday
        $validData['birthday'] = \Socialcommunity\Profile\Helper\Helper::prepareBirthday($data['birthday']);
        if (!$validData['birthday']) {
            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_INVALID_BIRTHDAY'), $redirectOptions);
            return;
        }

        // Prepare gender.
        $validData['gender'] = JStringHelper::trim(ArrayHelper::getValue($data, 'gender'));
        if (!in_array($validData['gender'], ['male', 'female'], true)) {
            $validData['gender'] = 'male';
        }

        $validData['user_id'] = $userId;

        try {
            // Store basic profile data.
            $basicRequest = new \Socialcommunity\Profile\Command\Request\Basic();
            $basicRequest
                ->setUserId($userId)
                ->setName($validData['name'])
                ->setBio($validData['bio'])
                ->setBirthday($validData['birthday'])
                ->setGender($validData['gender']);

            $gateway = new \Socialcommunity\Profile\Command\Gateway\Joomla\StoreBasic(JFactory::getDbo());
            $command = new \Socialcommunity\Profile\Command\StoreBasic($basicRequest);
            $command->setGateway($gateway);
            $command->handle();

            // Update user account name.
            $gateway = new \Socialcommunity\Account\Command\Gateway\Joomla\UpdateName(JFactory::getDbo());
            $command = new \Socialcommunity\Account\Command\UpdateName($basicRequest);
            $command->setGateway($gateway);
            $command->handle();
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_PROFILE_SAVED'), $redirectOptions);
    }
}
