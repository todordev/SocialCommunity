<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \Joomla\String\StringHelper;

// no direct access
defined('_JEXEC') or die;

class SocialcommunityViewSocialprofiles extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $form;
    protected $item;

    /**
     * @var array
     */
    protected $items;

    protected $option;

    protected $layout;
    protected $layoutData;
    protected $mediaFolder;
    protected $userId;
    protected $fileForCropping;
    protected $displayRemoveButton;
    protected $activeMenu;
    protected $googlePlusEnabled = false;
    protected $facebookEnabled;
    protected $facebookLoginUrl;
    protected $facebookDisconnectUrl;
    protected $twitterEnabled;
    protected $twitterLoginUrl;
    protected $twitterDisconnectUrl;

    protected $pageclass_sfx;

    /**
     * @var $app JApplicationSite
     */
    protected $app;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise a Error object.
     *
     * @see     JViewLegacy::loadTemplate()
     * @since   12.2
     */
    public function display($tpl = null)
    {
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->get('option');

        $this->userId = JFactory::getUser()->get('id');
        if (!$this->userId) {
            $this->app->enqueueMessage(JText::_('COM_SOCIALCOMMUNITY_ERROR_NOT_LOG_IN'), 'notice');
            $this->app->redirect(JRoute::_('index.php?option=com_users&view=login', false));

            return null;
        }

        $model = $this->getModel();

        $this->form   = $model->getForm();
        $this->state  = $model->getState();
        $this->params = $this->state->get('params');

        $this->items = (array)$model->getItems($this->userId);

        $this->facebookEnabled = (bool)$this->params->get('facebook_connect');
        if ($this->facebookEnabled) {
            $this->prepareFacebook();
        }

        $this->twitterEnabled = (bool)$this->params->get('twitter_connect');
        if ($this->twitterEnabled) {
            $this->prepareTwitter();
        }

        // @todo Retrive user contacts when it is possible
        /*$this->googlePlusEnabled = (bool)$this->params->get('googleplus_connect');
        if ($this->googlePlusEnabled) {
            $this->prepareGooglePlus();
        }*/

        $this->prepareDocument();

        return parent::display($tpl);
    }

    /**
     * Prepares the document.
     */
    protected function prepareDocument()
    {
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if (!$this->params->get('metadesc')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->params->get('metadesc'));
        }

        // Meta keywords
        if (!$this->params->get('metakey')) {
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetaData('keywords', $this->params->get('metakey'));
        }

        // Create and add title into breadcrumbs.
        $this->activeMenu    = $this->app->getMenu()->getActive();
        if (array_key_exists('view', $this->activeMenu->query) and strcmp('socialprofiles', $this->activeMenu->query['view']) !== 0) {
            $pathway = $this->app->getPathway();
            $pathway->addItem(JText::_('COM_SOCIALCOMMUNITY_CONNECT_PROFILE'));
        }
    }

    private function preparePageHeading()
    {
        // Prepare page heading
        if ($this->activeMenu) {
            $this->params->def('page_heading', $this->params->get('page_title', $this->activeMenu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_SOCIALCOMMUNITY_CONNECT_PROFILE_TITLE'));
        }
    }

    private function preparePageTitle()
    {
        // Prepare page title
        $title = JText::_('COM_SOCIALCOMMUNITY_CONNECT_PROFILE');

        // Add title before or after Site Name
        if (!$title) {
            $title = $this->app->get('sitename');
        } elseif ($this->app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ($this->app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    protected function prepareFacebook()
    {
        $service = array();
        foreach ($this->items as $item) {
            if (strcmp('facebook', $item['service']) === 0) {
                $service = $item;
                break;
            }
        }

        if ($service) {
            $disconnectUrl = '/index.php?option=com_socialcommunity&task=socialprofiles.fbdisconnect&'.JSession::getFormToken().'=1';

            if (!$service['expires_at']) {
                $this->facebookDisconnectUrl = $disconnectUrl;

                return;
            }

            // Check the period if exists.
            $date  = new DateTime($service['expires_at']);
            $date2 = new DateTime();
            if ($date > $date2) {
                $this->facebookDisconnectUrl = $disconnectUrl;
                return;
            }
        }

        if (!$this->facebookDisconnectUrl) {
            try {
                $fb = new Facebook\Facebook([
                    'app_id'                => $this->params->get('facebook_app_id'), // Replace {app-id} with your app id
                    'app_secret'            => $this->params->get('facebook_app_secret'),
                    'default_graph_version' => 'v2.4',
                ]);

                $helper = $fb->getRedirectLoginHelper();

                $permissions            = ['email', 'public_profile', 'user_friends']; // Optional permissions
                $this->facebookLoginUrl = $helper->getLoginUrl(JUri::root() . 'index.php?option=com_socialcommunity&task=socialprofiles.fbconnect&' . JSession::getFormToken() . '=1', $permissions);
            } catch (Exception $e) {
                JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
                $this->facebookEnabled = false;
                return;
            }
        }
    }

    protected function prepareTwitter()
    {
        // Get the data for Twitter.
        $service = array();
        foreach ($this->items as $item) {
            if (strcmp('twitter', $item['service']) === 0) {
                $service = $item;
                break;
            }
        }

        // Check Access Token expiration.
        if ($service) {
            $disconnectUrl = '/index.php?option=com_socialcommunity&task=socialprofiles.tdisconnect&'.JSession::getFormToken().'=1';

            if (!$service['expires_at']) {
                $this->twitterDisconnectUrl = $disconnectUrl;
                return;
            }

            // Check the period if exists.
            $date  = new DateTime($service['expires_at']);
            $date2 = new DateTime();
            if ($date > $date2) {
                $this->twitterDisconnectUrl = $disconnectUrl;
                return;
            }
        }

        // If there is no Access Token, make a request to get one.
        if (!$this->twitterDisconnectUrl) {
            try {
                $filter = JFilterInput::getInstance();

                // Get URI
                $uri         = JUri::getInstance();
                $callbackUrl = $filter->clean($uri->getScheme() . '://' . $uri->getHost()) . '/index.php?option=com_socialcommunity&task=socialprofiles.tconnect&' . JSession::getFormToken() . '=1';

                $connection    = new Abraham\TwitterOAuth\TwitterOAuth($this->params->get('twitter_consumer_key'), $this->params->get('twitter_consumer_secret'));
                $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $callbackUrl));

                $this->twitterLoginUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
            } catch (Exception $e) {
                JLog::add($e->getMessage(), JLog::ERROR, 'com_socialcommunity');
                $this->twitterEnabled = false;
                return;
            }
        }
    }

    /**
     * @todo Complete connection to Google Plus when it is possible to retrieve user ID and names from Google Plus contacts.
     */
    protected function prepareGooglePlus()
    {
        JHtml::_('jquery.framework');

        $this->googlePlusConnected = false;

        $version = new Socialcommunity\Version();

//        $this->document->addCustomTag('<meta name="google-signin-client_id" content="'.StringHelper::trim($this->params->get('googleplus_app_id')).'">');
        $this->document->addScript('https://apis.google.com/js/client:platform.js?onload=googlePlusConnect', [], ['async' => true, 'defer' => true]);
        $this->document->addScript('../../media/com_socialcommunity/js/site/spgpconnect.js?_='.$version->getShortVersion());
        $this->document->addScriptDeclaration('
        function googlePlusConnect() {
            gapi.load(\'auth2\', function() {
                auth2 = gapi.auth2.init({
                  client_id: \''.StringHelper::trim($this->params->get('googleplus_app_id')).'\',
                  scope: \'profile\'
                });
            })
        };
        ');

        JText::script('COM_SOCIALCOMMUNITY_CONNECT_GOOGLEPLUS');
        JText::script('COM_SOCIALCOMMUNITY_DISCONNECT_GOOGLEPLUS');
    }
}
