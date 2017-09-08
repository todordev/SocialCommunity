<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

// No direct access
defined('_JEXEC') or die;

/**
 * Form controller class.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerContact extends Prism\Controller\Form\Frontend
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = (int)JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );

            $this->displayNotice(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        $data            = $this->input->post->get('jform', array(), 'array');
        $redirectOptions = array(
            'view'   => 'form',
            'layout' => 'contact',
        );

        $model = $this->getModel();
        /** @var $model SocialcommunityModelContact */

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

        try {
            // Store encrypted data.
            $app     = \JFactory::getApplication();

            // Prepare conditions.
            $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
            $conditions = new Conditions();
            $conditions->addCondition($conditionUserId);

            // Prepare database request.
            $databaseRequest = new Request();
            $databaseRequest->setConditions($conditions);

            $gateway    = new \Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
            $repository = new \Socialcommunity\Profile\Contact\Repository($gateway);

            $contact    = $repository->fetch($databaseRequest);
            if (!$contact->getId()) {
                $contact->setUserId($userId);
            }

            $contact->setAddress($validData['address']);
            $contact->setPhone($validData['phone']);

            // Generate new secret key.
            $password  = $app->get('secret').$userId;
            $key       = \Defuse\Crypto\KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
            $secretKey = $key->saveToAsciiSafeString();

            $cryptor = new \Socialcommunity\Profile\Contact\Cryptor($secretKey, $password);
            $contact = $cryptor->encrypt($contact);

            $contact->setSecretKey($secretKey);
            $repository->store($contact);

            // Store the data that is part of the profile and it is not encrypted.
            $request = new \Socialcommunity\Profile\Command\Request\Contact();
            $request
                ->setUserId($userId)
                ->setCountryCode($validData['country_code'])
                ->setLocationId($validData['location_id'])
                ->setWebsite($validData['website']);

            $gateway = new \Socialcommunity\Profile\Command\Gateway\Joomla\StoreContact(JFactory::getDbo());
            $command = new \Socialcommunity\Profile\Command\StoreContact($request);
            $command->setGateway($gateway);
            $command->handle();

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_CONTACTS_SAVED'), $redirectOptions);
    }
}
