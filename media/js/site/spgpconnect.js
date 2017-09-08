jQuery(document).ready(function() {

    let $signInButton = jQuery('#js-googleplus-signin');
    let $signOutButton = jQuery('#js-googleplus-signout');

    $signInButton.on('click', function() {
        auth2.grantOfflineAccess().then(signInCallback);
    });

    $signOutButton.on('click', function() {
        auth2.disconnect();
        $signInButton.show();
        $signOutButton.hide();
    });

    function signInCallback(authResult) {
        if (authResult['code']) {

            // Hide the sign-in button now that the user is authorized, for example:
            $signInButton.hide();
            $signOutButton.show();

            let formData = {
                option: 'com_socialcommunity',
                task: 'task.gpconnect',
                token: '',
                code: authResult['code']
            };

            formData['token'] = '';

            jQuery.ajax({
                type: "POST",
                url: "index.php",
                dataType: "text json",
                data: formData,
                processData: false,
                // Always include an `X-Requested-With` header in every AJAX request,
                // to protect against CSRF attacks.
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                contentType: 'application/octet-stream; charset=utf-8'
            }).done(function (response) {
                if (response.success) {
                    Prism.message.show(response.message);
                }
            });

        } else {
            // There was an error.
        }

    }

});


