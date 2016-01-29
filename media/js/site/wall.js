jQuery(document).ready(function() {

	// Initialize symbol length indicator
	jQuery('#sc-wall-textarea').maxlength({
		alwaysShow: true,
		allowOverMax: true,
		appendToParent: true,
		showOnReady: true,
		customMaxAttribute: 140
	});

	jQuery("#js-sc-wall-form").on("submit", function(event){

		event.preventDefault();

		var $textArea = jQuery('#sc-wall-textarea');

		if ($textArea.hasClass('overmax')) {
			var message = Joomla.JText._('COM_SOCIALCOMMUNITY_LENGTH_POST_D');
			PrismUIHelper.displayMessageWarning(Joomla.JText._('COM_SOCIALCOMMUNITY_WARNING'), message.replace('%d', 140));
			return false;
		}

		var postContent   =  $textArea.val();

        // Do not post empty string.
        if (!postContent) {
            return false;
        }

		var fields = {
			task: 'wall.storePost',
			format: 'raw',
			content: postContent
		};
		
		jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_socialcommunity",
			dataType: "html",
			data: fields
		}).done(function(response) {

            if (response != ''){
				jQuery('#js-sc-wall-posts').prepend(response);
                $textArea.val('');
            }

		});
		
	});

	jQuery('#js-sc-wall-posts').on('click', '.js-wall-post-remove', function(event) {
        event.preventDefault();

        var postId = jQuery(this).data('post-id');

        var fields = {
            id: postId,
            task: 'wall.removePost',
            format: 'raw'
        };

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_socialcommunity",
            dataType: "text json",
            data: fields
        }).done(function(response) {

            if (response.success) {
                jQuery('#sc-wall-post-'+postId).remove();
            }

        });

    });

});
