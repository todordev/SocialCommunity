<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact;

use Prism\Database\Request\Request;
use Prism\Domain;
use Socialcommunity\Profile\Contact\Gateway\ContactGateway;

/**
 * This class provides a glue between persistence layer and profile object.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Contact
 */
class Repository extends Domain\Repository
{
    /**
     * @var ContactGateway
     */
    protected $gateway;

    /**
     * @var Cryptograph
     */
    protected $cryptor;

    /**
     * Repository constructor.
     *
     * <code>
     * $gateway     = new Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Socialcommunity\Profile\Contact\Repository($gateway);
     * </code>
     *
     * @param ContactGateway $gateway
     */
    public function __construct(ContactGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Load the data from database and return an entity.
     *
     * <code>
     * $contactId  = 1;
     *
     * $gateway     = new Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Socialcommunity\Profile\Contact\Repository($gateway);
     *
     * $contact     = $repository->findById($contactId);
     * </code>
     *
     * @param int $id
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Contact
     */
    public function fetchById($id, Request $request = null)
    {
        if (!$id) {
            throw new \InvalidArgumentException('There is no ID.');
        }

        $contact = new Contact();

        $data = $this->gateway->fetchById($id, $request);
        if (count($data) > 0) {
            $contact->setId($data['id']);
            $contact->setAddress($data['address']);
            $contact->setPhone($data['phone']);
            $contact->setSecretKey($data['secret_key']);
            $contact->setUserId($data['user_id']);
        }

        return $contact;
    }

    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'user_id' => 1
     * );
     *
     * $gateway     = new Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Socialcommunity\Profile\Contact\Repository($gateway);
     *
     * $contact     = $repository->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Contact
     */
    public function fetch(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetch($request);

        $contact = new Contact();
        if (count($data) > 0) {
            $contact->setId($data['id']);
            $contact->setAddress($data['address']);
            $contact->setPhone($data['phone']);
            $contact->setSecretKey($data['secret_key']);
            $contact->setUserId($data['user_id']);
        }

        return $contact;
    }

    /**
     * Store the data from contact object to database.
     *
     * <code>
     * $gateway     = new Socialcommunity\Profile\Contact\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Socialcommunity\Profile\Contact\Repository($gateway);
     *
     * $repository->store($contact);
     * </code>
     *
     * @param Contact  $contact
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function store(Contact $contact)
    {
        if ($contact->getId()) {
            $this->gateway->updateObject($contact);
        } else {
            $this->gateway->insertObject($contact);
        }
    }
}
