<?php
/**
 * @package      Socialcommunity
 * @subpackage   Version
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity;

/**
 * Information about package version.
 *
 * @package      Socialcommunity
 * @subpackage   Version
 */
class Version
{
    /**
     * Extension name
     *
     * @var string
     */
    public $product = 'Socialcommunity';

    /**
     * Main Release Level
     *
     * @var integer
     */
    public $release = '3';

    /**
     * Sub Release Level
     *
     * @var integer
     */
    public $devLevel = '0';

    /**
     * Release Type
     *
     * @var integer
     */
    public $releaseType = 'Lite';

    /**
     * Development Status
     *
     * @var string
     */
    public $devStatus = 'Stable';

    /**
     * Date
     *
     * @var string
     */
    public $releaseDate = '08 September, 2017';

    /**
     * Minimum required version of Prism library.
     *
     * @var string
     */
    public $requiredPrismVersion = '1.20';

    /**
     * License
     *
     * @var string
     */
    public $license = '<a href="http://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">GNU/GPLv3</a>';

    /**
     * Copyright Text
     *
     * @var string
     */
    public $copyright = '&copy; 2017 ITPrism. All rights reserved.';

    /**
     * URL
     *
     * @var string
     */
    public $url = '<a href="http://itprism.com/free-joomla-extensions/others/open-source-social-network" target="_blank">Social Community</a>';

    /**
     * Backlink
     *
     * @var string
     */
    public $backlink = '<div style="width:100%; text-align: left; font-size: xx-small; margin-top: 10px;"><a href="http://itprism.com/free-joomla-extensions/others/open-source-social-network" target="_blank">Joomla! Social Community</a></div>';

    /**
     * Developer
     *
     * @var string
     */
    public $developer = '<a href="http://itprism.com" target="_blank">ITPrism</a>';

    /**
     *  Build long format of the version text.
     *
     * @return string Long format version.
     */
    public function getLongVersion()
    {
        return
            $this->product . ' ' . $this->release . '.' . $this->devLevel . ' ' .
            $this->devStatus . ' ' . $this->releaseDate;
    }

    /**
     *  Build medium format of the version text.
     *
     * @return string Medium format version.
     */
    public function getMediumVersion()
    {
        return
            $this->release . '.' . $this->devLevel . ' ' .
            $this->releaseType . ' ( ' . $this->devStatus . ' )';
    }

    /**
     *  Build short format of the version text.
     *
     * @return string Short version format.
     */
    public function getShortVersion()
    {
        return $this->release . '.' . $this->devLevel;
    }
}
