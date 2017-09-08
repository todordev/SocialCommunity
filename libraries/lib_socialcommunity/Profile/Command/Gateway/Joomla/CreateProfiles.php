<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command\Gateway\Joomla;

use Prism\Database\JoomlaDatabase;
use Socialcommunity\Profile\Helper\JoomlaHelper;
use Socialcommunity\Profile\Command\Gateway\CreateProfilesGateway;

/**
 * Joomla database gateway.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command\Gateway
 */
class CreateProfiles extends JoomlaDatabase implements CreateProfilesGateway
{
    /**
     * Create profiles.
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function create()
    {
        // Check for a orphan profiles.
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from($this->db->quoteName('#__users', 'a'))
            ->leftJoin($this->db->quoteName('#__itpsc_profiles', 'b') . ' ON a.id = b.user_id')
            ->where('b.user_id IS NULL');

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        // Create profiles.
        $newProfiles = array();
        foreach ($results as $result) {
            $alias          = JoomlaHelper::generateAlias($result['name']);
            $newProfiles[]  = (int)$result['id'] .','. $this->db->quote($result['name']) .','.$this->db->quote($alias);
        }

        if (count($newProfiles) > 0) {
            $query = $this->db->getQuery(true);
            $query
                ->insert($this->db->quoteName('#__itpsc_profiles'))
                ->columns($this->db->quoteName(['user_id', 'name', 'alias']))
                ->values($newProfiles);

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
}
