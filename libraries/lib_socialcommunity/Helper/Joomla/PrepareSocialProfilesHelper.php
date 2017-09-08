<?php
/**
 * @package      Socialcommunity\Helper
 * @subpackage   Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Helper\Joomla;

use Socialcommunity\Socialprofile\Mapper;
use Socialcommunity\Socialprofile\Repository;
use Socialcommunity\Socialprofile\Gateway\JoomlaGateway;
use Socialcommunity\Socialprofile\Socialprofile;
use Prism\Helper\HelperInterface;
use Prism\Utilities\ArrayHelper;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

/**
 * Prepare social profiles for every profile item.
 *
 * @package      Socialcommunity\Helper
 * @subpackage   Joomla
 */
class PrepareSocialProfilesHelper implements HelperInterface
{
    /**
     * Prepare the parameters of the items.
     *
     * @param array $data
     * @param array $options
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $usersIds = ArrayHelper::getIds($data, 'user_id');

            // Prepare conditions.
            $conditionUserIds    = new Condition(['column' => 'user_id', 'value' => $usersIds, 'operator' => 'IN', 'table' => 'a']);
            $conditions          = new Conditions();
            $conditions->addSpecificCondition('user_ids', $conditionUserIds);

            // Prepare database request.
            $databaseRequest = new Request();
            $databaseRequest->setConditions($conditions);

            // Prepare social profiles
            $mapper         = new Mapper(new JoomlaGateway(\JFactory::getDbo()));
            $repository     = new Repository($mapper);
            $socialProfiles = $repository->fetchCollection($databaseRequest);

            if (count($socialProfiles) > 0) {
                $services = array();

                /** @var Socialprofile $socialProfile */
                foreach ($socialProfiles as $socialProfile) {
                    $services[$socialProfile->getUserId()][$socialProfile->getService()] = $socialProfile->getLink();
                }

                foreach ($data as $item) {
                    if (array_key_exists($item->user_id, $services)) {
                        $item->socialProfiles = $services[$item->user_id];
                    }
                }
            }
        }
    }
}
