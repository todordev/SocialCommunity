window.addEvent('domready', function(){

	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };

    // Load locations from the server
    jQuery('#jform_location_preview').typeahead({
        ajax : {
            url: "index.php?option=com_socialcommunity&format=raw&task=profile.loadLocation",
            method: "get",
            triggerLength: 3,
            displayField: 'text',
            valueField: 'value',
            preDispatch: function(query) {
                return {
                    search: query,
                    country_id: jQuery('#jform_country_id').val()
                }
            },
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