<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if(!$this->item->image){
	$image = 'media/com_socialcommunity/images/no_profile_200x200.png';
	$imageSquare = 'media/com_socialcommunity/images/no_profile_50x50.png';
	$imageAlt = '';
} else {
	$image = $this->mediaFolder.'/'.$this->item->image;
	$imageSquare = $this->mediaFolder.'/'.$this->item->image_square;
	$imageAlt = 'alt="' . $this->escape($this->item->name).'"';
}

$layoutData = new stdClass();
$layoutData->profileLink = JRoute::_(SocialCommunityHelperRoute::getProfileRoute($this->item->user_id), false);
$layoutData->imageAlt = $imageAlt;
$layoutData->imageSquare = $imageSquare;
$layoutData->name = $this->escape($this->item->name);
$layoutData->alias = $this->escape($this->item->alias);
?>
<div class="scprofile-wall<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->item->event->beforeDisplayContent) {
		echo $this->item->event->beforeDisplayContent;
	} ?>

	<div class="row" itemscope itemtype="http://schema.org/Person">
		<div class="col-md-3">
			<h3 itemprop="name"><?php echo $this->item->name;?></h3>
			<img src="<?php echo $image;?>" <?php echo $imageAlt;?> itemprop="image" />

			<?php if ($this->item->event->beforeDisplayProfileContent) {
				echo $this->item->event->beforeDisplayProfileContent;
			} ?>

		</div>
		<div class="col-md-9 sc-wall-wrapper well">
			<form method="post" action="<?php echo JRoute::_(''); ?>" id="js-sc-wall-form" class="sc-wall-form">
				<label class="sr-only" for="sc-wall-textarea"><?php echo JText::_('COM_SOCIALCOMMYNITY_SHARE_SOMETHING'); ?></label>
				<textarea name="content" class="col-md-12" id="sc-wall-textarea" placeholder="<?php echo JText::_('COM_SOCIALCOMMYNITY_WHAT_WILL_SHARE'); ?>" ></textarea>

				<div class="clearfix"></div>
				<div class="row ">
					<div class="col-md-8">

					</div>
					<div class="col-md-1">
						<div id="js-sc-wall-counter">

						</div>
					</div>
					<div class="col-md-3">
						<button class="btn btn-primary pull-right btn-block" type="submit">
							<?php echo JText::_('COM_SOCIALCOMMYNITY_SHARE'); ?>
						</button>
					</div>
				</div>

			</form>
			<div class="clearfix"></div>
			<div id="js-sc-wall-posts" class="list-group mt-20">
				<?php
				$layout      = new JLayoutFile('wall_post');
				foreach($this->wallPosts as $post) {
					$layoutData->id = (int)$post['id'];
					$layoutData->content = $this->escape($post['content']);

					$layoutData->created = JHtml::_('socialcommunity.created', $post['created'], $this->timezone);

    	    		echo $layout->render($layoutData);
				} ?>
			</div>
			<?php if ($this->item->event->afterDisplayProfileContent) {
				echo $this->item->event->afterDisplayProfileContent;
			} ?>

		</div>
	</div>

	<?php if ($this->item->event->afterDisplayContent) {
		echo $this->item->event->afterDisplayContent;
	} ?>
</div>