<?php
/**
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Socialcommunity\Profile\Command;

use Prism\Command\Command;
use League\Flysystem\Filesystem;
use Socialcommunity\Value\Profile\Image;

/**
 * Update profile image.
 *
 * @package      Socialcommunity\Profile
 * @subpackage   Command
 */
class DeleteImage implements Command
{
    /**
     * @var Image
     */
    protected $profileImage;

    protected $mediaFolder;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Store basic profile data command constructor.
     *
     * @param Image      $profileImage
     * @param Filesystem $filesystem
     * @param string     $mediaFolder
     */
    public function __construct(Image $profileImage, Filesystem $filesystem, $mediaFolder)
    {
        $this->profileImage = $profileImage;
        $this->filesystem   = $filesystem;
        $this->mediaFolder  = $mediaFolder;
    }

    public function handle()
    {
        // Delete the profile pictures.
        if ($this->profileImage->image and $this->filesystem->has($this->mediaFolder . '/' . $this->profileImage->image)) {
            $this->filesystem->delete($this->mediaFolder . '/' . $this->profileImage->image);
        }

        if ($this->profileImage->image_small and $this->filesystem->has($this->mediaFolder . '/' . $this->profileImage->image_small)) {
            $this->filesystem->delete($this->mediaFolder . '/' . $this->profileImage->image_small);
        }

        if ($this->profileImage->image_square and $this->filesystem->has($this->mediaFolder . '/' . $this->profileImage->image_square)) {
            $this->filesystem->delete($this->mediaFolder . '/' . $this->profileImage->image_square);
        }

        if ($this->profileImage->image_icon and $this->filesystem->has($this->mediaFolder . '/' . $this->profileImage->image_icon)) {
            $this->filesystem->delete($this->mediaFolder . '/' . $this->profileImage->image_icon);
        }
    }
}
