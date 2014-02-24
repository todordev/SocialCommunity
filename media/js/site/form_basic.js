jQuery(document).ready(function($) {
	
	// Style file input
	jQuery('.fileupload').fileuploadstyle();
	
	jQuery('#jform_bio').attr("maxlength", 512);
	
	// Initialize symbol length indicator
	jQuery('#jform_bio').maxlength({
		alwaysShow: true,
		placement: 'bottom-right'
	});
	
});