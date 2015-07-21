jQuery(document).ready(function($) {
	
	// Style file input
	jQuery('#jform_photo').filestyle({
        input: true,
        buttonBefore: true,
        iconName: "glyphicon-upload",
        buttonText: Joomla.JText._('COM_SOCIALCOMMUNITY_SELECT_FILE'),
        badge: false
    });
	
	// Initialize symbol length indicator
	jQuery('#jform_bio').maxlength({
		alwaysShow: true,
		placement: 'bottom-right'
	});

    jQuery('#js-btn-remove-image').on("click", function(event) {
        event.preventDefault();

        if (confirm(Joomla.JText._('COM_SOCIALCOMMUNITY_QUESTION_REMOVE_IMAGE'))) {
            window.location = jQuery(this).attr("href");
        }
    });
	
});