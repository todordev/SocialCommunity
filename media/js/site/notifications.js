jQuery(document).ready(function() {

	jQuery("#js-sc-notifications").on("click", ".js-sc-btn-remove-notification", function(event){

		event.preventDefault();
		
		let id 		  = jQuery(this).data("element-id");
        let elementId = "#js-sc-note-element"+id;

        let fields = {
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
                Prism.message.show(response.message);
            } else {
                jQuery(elementId).fadeOut('slow', function () {
                    jQuery(this).remove();
                });

                Prism.message.show(response.message);
            }
		});
	});
});
