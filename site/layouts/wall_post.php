<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="list-group-item" id="sc-wall-post-<?php echo $displayData->id; ?>">
    <div class="row">
        <div class="col-md-1">
            <a href="<?php echo $displayData->profileLink; ?>" rel="nofollow">
                <img src="<?php echo $displayData->imageSquare; ?>" <?php echo $displayData->imageAlt;?> />
            </a>
        </div>

        <div class="col-md-11">
            <h4 class="list-group-item-heading">
                <a href="<?php echo $displayData->profileLink; ?>" rel="nofollow">
                    <?php echo $displayData->name; ?>
                </a>
                &nbsp;
                <span class="sc-user-alias">@<?php echo $displayData->alias; ?></span>
                &nbsp;
                <span class="sc-post-created"><?php echo $displayData->created; ?></span>
            </h4>
            <p class="list-group-item-text"><?php echo $displayData->content; ?></p>
        </div>
    </div>
    <div class="sc-user-post-footbar">
        <div class="row">

            <div class="col-md-11">
            </div>

            <div class="col-md-1">
                <a href="javascript: void(0);" role="button" class="btn btn-danger btn-xs js-wall-post-remove" data-post-id="<?php echo $displayData->id; ?>">
                    <span class="fa fa-trash"></span>
                </a>
            </div>
        </div>
    </div>
</div>