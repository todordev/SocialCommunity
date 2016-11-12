<?php
/**
 * @package      SocialCommunity\Profile
 * @subpackage   Validators
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Validator\Profile;

use Prism\Validator\ValidatorInterface;

defined('JPATH_BASE') or die;

/**
 * This class provides functionality for validation notification owner.
 *
 * @package      SocialCommunity\Profile
 * @subpackage   Validators
 */
class Alias implements ValidatorInterface
{
    protected $db;

    protected $user_id;
    protected $alias;

    /**
     * Initialize the object.
     *
     * <code>
     * $userId = 1;
     * $alias = 'john-dow';
     *
     * $owner = new Socialcommunity\Validator\Profile\Alias(JFactory::getDbo(), $alias, $userId);
     * </code>
     *
     * @param \JDatabaseDriver $db Database object.
     * @param string          $alias
     * @param int             $userId
     */
    public function __construct(\JDatabaseDriver $db, $alias, $userId = 0)
    {
        $this->db        = $db;
        $this->user_id   = $userId;
        $this->alias     = $alias;
    }

    /**
     * Check for valid profile alias or if it exists.
     *
     * <code>
     * $userId = 1;
     * $alias = 'john-dow';
     *
     * $owner = new Socialcommunity\Validator\Profile\Alias(JFactory::getDbo(), $alias, $userId);
     * if(!$owner->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->alias or (preg_match('/[^A-Z0-9\-]/i', $this->alias))) {
            return false;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__itpsc_profiles', 'a'))
            ->where('a.alias = ' . $this->db->quote($this->alias));

        if ($this->user_id > 0) {
            $query->where('a.user_id != ' . (int)$this->user_id);
        }

        $this->db->setQuery($query, 0, 1);

        $result = $this->db->loadResult();
        return (bool)!$result;
    }
}
