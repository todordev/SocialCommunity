jQuery(document).ready(function() {

    let $locationElement = jQuery('#jform_location_preview');
    let $locationIdElement = jQuery('#jform_location_id');

    let $countryCodeElement = jQuery('#jform_country_code');

    let fields = {
        'task': 'contact.loadLocation',
        'format': 'raw'
    };

    $locationElement.autocomplete({
        serviceUrl: '/index.php?option=com_socialcommunity',
        params: fields,
        minChars: 3,
        onSearchStart: function(query) {
            query.country_code = $countryCodeElement.val();
        },
        onSelect: function (suggestion) {
            $locationIdElement.val(suggestion.data);
        },
        transformResult: function(response) {
            let r = JSON.parse(response);

            return {
                suggestions: jQuery.map(r.data, function(dataItem) {
                    return { value: dataItem.text, data: dataItem.value};
                })
            };
        }
    });
});