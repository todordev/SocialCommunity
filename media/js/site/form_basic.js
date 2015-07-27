jQuery(document).ready(function($) {
	
	// Style file input
	jQuery('#jform_photo').fileinput({
        browseLabel: Joomla.JText._('COM_SOCIALCOMMUNITY_SELECT_FILE'),
        browseClass: "btn btn-default",
        showUpload: false,
        showPreview: false,
        removeLabel: Joomla.JText._('COM_SOCIALCOMMUNITY_REMOVE'),
        removeClass: "btn btn-danger",
        layoutTemplates: {
            main1:
            "<div class=\'input-group {class}\'>\n" +
            "   <div class=\'input-group-btn\'>\n" +
            "       {browse}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>"
        }
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