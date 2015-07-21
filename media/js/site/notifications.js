jQuery(document).ready(function() {

	jQuery("#js-sc-notifications").on("click", ".js-sc-btn-remove-notification", function(event){

		event.preventDefault();
		
		var id 		  =  jQuery(this).data("element-id");
		var elementId = "#js-sc-note-element"+id;

		var fields = {
			id: id,
			format: "raw"
		};
		
		jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_socialcommunity&task=notification.remove",
			dataType: "text json",
			data: fields
		}).done(function(response) {

            if (!response.success){
                PrismUIHelper.displayMessageFailure(response.title, response.text);
            } else {
                jQuery(elementId).fadeOut('slow', function () {
                    jQuery(this).remove();
                });

                PrismUIHelper.displayMessageSuccess(response.title, response.text);
            }

		});
		
	});
});
