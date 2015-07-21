jQuery(document).ready(function($) {

    // Load locations from the server
    var $inputTypeahead = jQuery('#jform_location_preview');
    $inputTypeahead.typeahead({
        minLength: 3,
        hint: false
    }, {
        source: function(query, syncResults, asyncResults) {

            jQuery.ajax({
                url: "index.php?option=com_socialcommunity&format=raw&task=contact.loadLocation",
                type: "get",
                data: {query: query},
                dataType: "text json",
                async: true,
                beforeSend : function() {
                    // Show ajax loader.
                    //$loader.show();
                }
            }).done(function(response){
                // Hide ajax loader.
                //$loader.hide();

                if (response.success === false) {
                    return false;
                }

                return asyncResults(response.data);
            });

        },
        async: true,
        limit: 5,
        display: "name"
    });

    $inputTypeahead.bind('typeahead:select', function(event, suggestion) {
        jQuery("#jform_location_id").attr("value", suggestion.id);
    });
});