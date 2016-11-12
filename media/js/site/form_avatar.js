jQuery(document).ready(function() {

    // Get the loader.
    var $loader  = jQuery("#js-thumb-fileupload-loader");

    /** Image Tools **/

    var aspectWidth  = socialCommunityOptions.imageWidth * 3;
    var aspectHeight = socialCommunityOptions.imageHeight * 2;

    // Set picture wrapper size.
    var $pictureWrapper = jQuery("#js-fixed-dragger-cropper");
    $pictureWrapper.css({
        width: aspectWidth,
        height: aspectHeight
    });

    // Initialize the cropper if image exists (browser has been reloaded).
    var $image = $pictureWrapper.find("img");
    if ($image) {
        initializeCropper($image, socialCommunityOptions.imageWidth, socialCommunityOptions.imageHeight, socialCommunityOptions.aspectRatio);
    }

    // Prepare the token as an object.
    var tokenObject = jQuery("#js-image-tools-form").serializeJSON();

    // Upload image.
    jQuery('#js-thumb-fileupload').fileupload({
        dataType: 'text json',
        formData: tokenObject,
        singleFileUploads: true,
        send: function() {
            $loader.show();
        },
        fail: function() {
            $loader.hide();
        },
        done: function (event, response) {

            if(!response.result.success) {
                PrismUIHelper.displayMessageFailure(response.result.title, response.result.text);
            } else {

                if ($image) {
                    $image.cropper("destroy");
                    $image.remove();
                }

                // Create new image.
                $image = jQuery('<img/>', {
                    src: response.result.data.image,
                    class: 'img-polaroid center-block'
                });
                $image.appendTo('#js-fixed-dragger-cropper');

                initializeCropper($image, socialCommunityOptions.imageWidth, socialCommunityOptions.imageHeight, socialCommunityOptions.aspectRatio);
            }

            // Hide ajax loader.
            $loader.hide();
        }
    });

    // Set event to the button "Cancel".
    jQuery("#js-crop-btn-cancel").on("click", function() {

        $image.cropper("destroy");
        $image.remove();

        jQuery("#js-image-tools").hide();

        // Add the token.
        var fields = PrismUIHelper.extend({format: 'raw', task: 'avatar.cancelImageCrop'}, tokenObject);

        jQuery.ajax({
            url: "index.php?option=com_socialcommunity",
            type: "POST",
            data: fields,
            dataType: "text json",
            beforeSend : function() {
                // Show ajax loader.
                $loader.show();
            }
        }).done(function(){
            // Hide ajax loader.
            $loader.hide();
        });
    });

    // Set event to the button "Crop Image".
    jQuery("#js-crop-btn").on("click", function(event) {

        var croppedData = $image.cropper("getData");

        // Prepare data.
        var data = {
            width: Math.round(croppedData.width),
            height: Math.round(croppedData.height),
            x: Math.round(croppedData.x),
            y: Math.round(croppedData.y)
        };

        // Add the token.
        var fields = PrismUIHelper.extend({format: 'raw', task: 'avatar.cropImage'}, data, tokenObject);

        jQuery.ajax({
            url: "index.php?option=com_socialcommunity",
            type: "POST",
            data: fields,
            dataType: "text json",
            beforeSend : function() {

                jQuery.isLoading({
                    text: Joomla.JText._('COM_SOCIALCOMMUNITY_CROPPING___'),
                    'tpl': '<span class="isloading-wrapper %wrapper%"><img src="'+socialCommunityOptions.url+'libraries/Prism/ui/images/loader_120x120.gif" width="120" height="120"/></span>'
                });

                jQuery("#js-image-tools").hide();

            }

        }).done(function(response) {

            if(!response.success) {
                PrismUIHelper.displayMessageFailure(response.title, response.text);
            } else {

                if ($image) {
                    $image.cropper("destroy");
                    $image.remove();
                }

                jQuery("#js-avatar-img").attr("src", response.data);

                // Display the button "Remove Image".
                jQuery("#js-btn-remove-image").show();

                jQuery.isLoading("hide");
            }

        });

    });

    jQuery("#js-btn-remove-image").on('click', function(){

    });

    function initializeCropper($image, imageWidth, imageHeight, aspectRatio) {

        var options = {
            autoCropArea: 0.6, // Center 60%
            multiple: false,
            dragCrop: false,
            dashed: false,
            movable: false,
            resizable: true,
            zoomable: false,
            minWidth: imageWidth,
            minHeight: imageHeight,
            built: function() {
                jQuery("#js-image-tools").show();
            }
        };

        if (aspectRatio) {
            options.aspectRatio = aspectRatio;
        }

        $image.cropper(options);
    }

});