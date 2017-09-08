<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \Prism\Database\Condition\Condition;
use \Prism\Database\Condition\Conditions;
use \Prism\Database\Request\Request;
use \Socialcommunity\Socialprofile\Mapper;
use \Socialcommunity\Socialprofile\Repository;
use \Socialcommunity\Socialprofile\Gateway\JoomlaGateway;

// No direct access
defined('_JEXEC') or die;

/**
 * Form controller class.
 *
 * @package     Socialcommunity
 * @subpackage  Components
 */
class SocialcommunityControllerSocialprofiles extends Prism\Controller\Form\Frontend
{
    public function fbconnect()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            'view' => 'socialprofiles',
            'Itemid' => $this->getMenuItemId()
        );

        $params = JComponentHelper::getParams('com_socialcommunity');
        
        $userId = (int)JFactory::getUser()->get('id');
        if (!$userId) {
            $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_USER'), $redirectOptions);
            return;
        }
        
        // Prepare conditions.
        $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
        $conditionService = new Condition(['column' => 'service', 'value' => 'facebook', 'operator'=> '=', 'table' => 'a']);
        $conditions = new Conditions();
        $conditions
            ->addCondition($conditionUserId)
            ->addCondition($conditionService);

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        // Get social profile if exists.
        $mapper     = new \Socialcommunity\Socialprofile\Mapper(new \Socialcommunity\Socialprofile\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new \Socialcommunity\Socialprofile\Repository($mapper);
        $profile    = $repository->fetch($databaseRequest);

        // Connect to facebook and get access token.
        $fb = new Facebook\Facebook([
            'app_id' => $params->get('facebook_app_id'), // Replace {app-id} with your app id
            'app_secret' => $params->get('facebook_app_secret'),
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Exception $e) {
            $this->displayError($e->getMessage(), $redirectOptions);
            return;
        }

        // Check access token.
        if ($accessToken === null) {
            if ($helper->getError()) {
                $this->displayError($helper->getErrorReason(), $redirectOptions);
            } else {
                $this->displayError('Bad request', $redirectOptions);
            }
            return;
        }

        // Get long lived access token.
        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                // The OAuth 2.0 client handler helps us manage access tokens
                $oAuth2Client = $fb->getOAuth2Client();
                $accessToken  = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $this->displayError('Error getting long-lived access token: '.$helper->getErrorReason(), $redirectOptions);
                return;
            }
        }

        // Get facebook user's ID and the link to his profile.
        $response   = $fb->get('/me?fields=id,link,picture', $accessToken->getValue());
        $graphUser  = $response->getGraphUser();

        // Prepare access token for encryption.
        $expiresAt    = $accessToken->getExpiresAt() ? $accessToken->getExpiresAt()->format('Y-m-d H:i:s') : '';
        $accessToken_ = new \Socialcommunity\Socialprofile\Token\AccessToken($accessToken->getValue(), $expiresAt);

        // Encrypt the access token.
        $app       = \JFactory::getApplication();
        $password  = $app->get('secret').$userId;
        $key       = \Defuse\Crypto\KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
        $secretKey = $key->saveToAsciiSafeString();

        $crypto       = new \Socialcommunity\Socialprofile\Token\Cryptor($secretKey, $password);
        $accessTokenEncrypted = $crypto->encrypt($accessToken_);

        // Prepare social profile data.
        $profile->setUserId($userId);
        $profile->setLink($graphUser['link']);

        /** @var \Facebook\GraphNodes\GraphPicture $graphPicture */
        $graphPicture = $graphUser['picture'];
        $profile->setImageSquare($graphPicture->getUrl());

        $profile->setServiceUserId($graphUser['id']);
        $profile->setService('facebook');
        $profile->setSecretKey($secretKey);
        $profile->setAccessToken($accessTokenEncrypted);

        $repository->store($profile);

        // Get user's friends.
        $response  = $fb->get('/me/friends', $accessToken->getValue());
        $friends   = $response->getGraphEdge()->asArray();

        $storeFriendsCommand = new \Socialcommunity\Socialprofile\Friend\Command\StoreFriends('facebook', $graphUser['id'], $friends);
        $storeFriendsCommand->setGateway(new \Socialcommunity\Socialprofile\Friend\Command\Gateway\Joomla\StoreFriends(JFactory::getDbo()));
        $storeFriendsCommand->handle();

        $this->displayMessage(JText::sprintf('COM_SOCIALCOMMUNITY_CONNECTED_SUCCESSFULLY_S', 'Facebook'), $redirectOptions);
    }

    public function fbdisconnect()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            'view' => 'socialprofiles',
            'Itemid' => $this->getMenuItemId()
        );

        $userId = (int)JFactory::getUser()->get('id');
        if (!$userId) {
            $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_USER'), $redirectOptions);
            return;
        }

        $removeAccessToken = new \Socialcommunity\Socialprofile\Command\RemoveAccessToken($userId, 'facebook');
        $removeAccessToken->setGateway(new \Socialcommunity\Socialprofile\Command\Gateway\Joomla\RemoveAccessToken(JFactory::getDbo()));
        $removeAccessToken->handle();

        $this->displayMessage(JText::sprintf('COM_SOCIALCOMMUNITY_DISCONNECTED_SUCCESSFULLY_S', 'Facebook'), $redirectOptions);
    }

    /**
     * Connect to Twitter and get user's friends.
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public function tconnect()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            'view' => 'socialprofiles',
            'Itemid' => $this->getMenuItemId()
        );

        $userId = (int)JFactory::getUser()->get('id');
        if (!$userId) {
            $this->displayError(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_USER'), $redirectOptions);
            return;
        }

        $params = JComponentHelper::getParams('com_socialcommunity');
        
        // ### Fetch social profile.
        
        // Prepare conditions.
        $conditions = new Conditions();
        $conditions
            ->addCondition(new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']))
            ->addCondition(new Condition(['column' => 'service', 'value' => 'twitter', 'operator'=> '=', 'table' => 'a']));

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        // Get social profile if exists.
        $mapper     = new Mapper(new JoomlaGateway(JFactory::getDbo()));
        $repository = new Repository($mapper);
        $profile    = $repository->fetch($databaseRequest);

        // ### End fetching social profile.
        
        // ### Get Access Token
        $app           = \JFactory::getApplication();
        
        $oauthVerifier = $this->input->get('oauth_verifier');
        $oauthToken    = $this->input->get('oauth_token');

        try {
            $connection  = new Abraham\TwitterOAuth\TwitterOAuth($params->get('consumer_key'), $params->get('consumer_secret'));
            $accessToken = $connection->oauth('oauth/access_token', array('oauth_token' => $oauthToken, 'oauth_verifier' => $oauthVerifier));

            $connection  = new Abraham\TwitterOAuth\TwitterOAuth($params->get('twitter_consumer_key'), $params->get('twitter_consumer_secret'), $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
            $userNode    = $connection->get('account/verify_credentials');

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            $this->displayError(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'), $redirectOptions);
            return;
        }

        // Prepare access token for encryption.
        $expiresAt    = new JDate();
        $expiresAt->add(new DateInterval('P30D'));
        $accessToken_ = new \Socialcommunity\Socialprofile\Token\AccessToken($accessToken['oauth_token'].'.'.$accessToken['oauth_token_secret'], $expiresAt->toSql());

        // Encrypt the access token.
        
        $password  = $app->get('secret').$userId;
        $key       = \Defuse\Crypto\KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
        $secretKey = $key->saveToAsciiSafeString();

        $crypto       = new \Socialcommunity\Socialprofile\Token\Cryptor($secretKey, $password);
        $accessTokenEncrypted = $crypto->encrypt($accessToken_);

        // Prepare social profile data.
        $profile->setUserId($userId);
        $profile->setLink('http://twitter.com/'.$userNode->screen_name);

        /** @var \Facebook\GraphNodes\GraphPicture $graphPicture */
        $profile->setImageSquare($userNode->profile_image_url);

        $profile->setServiceUserId($userNode->id);
        $profile->setService('twitter');
        $profile->setSecretKey($secretKey);
        $profile->setAccessToken($accessTokenEncrypted);

        $repository->store($profile);

        // ### Get user's friends.

        try {
            $attr = array();
            $friends = array();
            $cursor  = 0;

            do {
                if ($cursor) {
                    $attr = ['cursor' => $cursor];
                }

                $friendsCollection = $connection->get('friends/list', $attr);

                if (!empty($friendsCollection->users)) {
                    foreach ($friendsCollection->users as $user) {
                        $friends[] = [
                            'id' => $user->id,
                            'name' => $user->name
                        ];
                    }
                }
                $cursor = isset($friendsCollection->next_cursor) ? $friendsCollection->next_cursor : 0;
            } while ($cursor != 0);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
            $this->displayError(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'), $redirectOptions);
            return;
        }

        $storeFriendsCommand = new \Socialcommunity\Socialprofile\Friend\Command\StoreFriends('twitter', $userNode->id, $friends);
        $storeFriendsCommand->setGateway(new \Socialcommunity\Socialprofile\Friend\Command\Gateway\Joomla\StoreFriends(JFactory::getDbo()));
        $storeFriendsCommand->handle();

        $this->displayMessage(JText::sprintf('COM_SOCIALCOMMUNITY_CONNECTED_SUCCESSFULLY_S', 'Twitter'), $redirectOptions);
    }

    public function tdisconnect()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            'view' => 'socialprofiles',
            'Itemid' => $this->getMenuItemId()
        );

        $userId = (int)JFactory::getUser()->get('id');
        if (!$userId) {
            $this->displayMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_INVALID_USER'), $redirectOptions);
            return;
        }

        $removeAccessToken = new \Socialcommunity\Socialprofile\Command\RemoveAccessToken($userId, 'twitter');
        $removeAccessToken->setGateway(new \Socialcommunity\Socialprofile\Command\Gateway\Joomla\RemoveAccessToken(JFactory::getDbo()));
        $removeAccessToken->handle();

        $this->displayMessage(JText::sprintf('COM_SOCIALCOMMUNITY_DISCONNECTED_SUCCESSFULLY_S', 'Twitter'), $redirectOptions);
    }

    public function testTwitterAccessToken()
    {

    }

    protected function getMenuItemId()
    {
        $app        = JFactory::getApplication();
        $menu       = $app->getMenu();
        $menuItems  = $menu->getItems('menutype', 'mainmenu');
        $menuItemId = 0;

        foreach ($menuItems as $menuItem) {
            if (array_key_exists('view', $menuItem->query) and strcmp('socialprofiles', $menuItem->query['view']) === 0) {
                $menuItemId = $menuItem->id;
                break;
            }
        }

        return $menuItemId;
    }
}
