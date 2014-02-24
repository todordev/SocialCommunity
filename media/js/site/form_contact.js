jQuery(document).ready(function($) {
	
	// Load locations from the server
	jQuery('#jform_location_preview').typeahead({
		
		ajax : {
			url: "index.php?option=com_socialcommunity&format=raw&task=contact.loadLocation",
			method: "get",
			triggerLength: 3,
			preProcess: function (response) {
				
	            if (response.success === false) {
	                return false;
	            }
	            
	            return response.data;
	        }
		},
		onSelect: function(item) {
			jQuery("#jform_location_id").attr("value", item.value);
		}
		
	});
	
});