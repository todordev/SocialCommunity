jQuery(document).ready(function($) {

    // Load locations from the server
    var $inputTypeahead = jQuery('#jform_location_preview');
    $inputTypeahead.typeahead({
        minLength: 3,
        hint: false
    }, {
        source: function(query, syncResults, asyncResults) {

            var country_id = jQuery('#jform_country_id').val();

            var fields = {
                query: query,
                format: 'raw',
                task: 'contact.loadLocation',
                country_id: country_id
            };

            jQuery.ajax({
                url: "index.php?option=com_socialcommunity",
                type: "GET",
                data: fields,
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
        limit: 10,
        display: "text",
        name: "value"
    });

    $inputTypeahead.bind('typeahead:select', function(event, suggestion) {
        jQuery("#jform_location_id").attr("value", suggestion.value);
    });
});