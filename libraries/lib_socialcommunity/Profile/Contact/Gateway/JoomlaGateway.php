<?php
/**
 * @package      Socialcommunity\Profile\Contact
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Contact\Gateway;

use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Request\Request;
use Socialcommunity\Profile\Contact\Contact;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile\Contact
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements ContactGateway
{
    use FetchMethods, FetchCollectionMethod;

    /**
     * Prepare the query by query builder.
     *
     * @param Request $request
     *
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery(Request $request = null)
    {
        $defaultFields  = ['a.id', 'a.phone', 'a.address', 'a.user_id', 'a.secret_key'];
        $fields = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->select('a.id, a.phone, a.address, a.user_id, a.secret_key')
            ->from($this->db->quoteName('#__itpsc_profilecontacts', 'a'));

        return $query;
    }

    public function insertObject(Contact $object)
    {
        $address  = $object->getAddress() ? $this->db->quote($object->getAddress()) : 'NULL';
        $phone    = $object->getPhone() ? $this->db->quote($object->getPhone()) : 'NULL';
        $key      = $object->getSecretKey() ? $this->db->quote($object->getSecretKey()) : 'NULL';

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__itpsc_profilecontacts'))
            ->set($this->db->quoteName('user_id') .'='. (int)$object->getUserId())
            ->set($this->db->quoteName('address') .'='. $address)
            ->set($this->db->quoteName('secret_key') .'='. $key)
            ->set($this->db->quoteName('phone') .'='. $phone);

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Contact $object)
    {
        $address    = $object->getAddress() ? $this->db->quote($object->getAddress()) : 'NULL';
        $phone    = $object->getPhone() ? $this->db->quote($object->getPhone()) : 'NULL';
        $key      = $object->getSecretKey() ? $this->db->quote($object->getSecretKey()) : 'NULL';

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__itpsc_profilecontacts'))
            ->set($this->db->quoteName('user_id') .'='. (int)$object->getUserId())
            ->set($this->db->quoteName('address') .'='. $address)
            ->set($this->db->quoteName('phone') .'='. $phone)
            ->set($this->db->quoteName('secret_key') .'='. $key)
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
