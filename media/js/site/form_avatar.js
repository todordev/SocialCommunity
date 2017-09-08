jQuery(document).ready(function() {

    // Get the loader.
    let $uploadLoader   = jQuery("#js-avatar-loader");
    let $btnImageRemove = jQuery('#js-btn-remove-image');
    let formToken       = jQuery("#js-form-token").attr('name');

    // Initialize the modal plugin.
    let $modal   = jQuery("#js-modal-wrapper").remodal({
        hashTracking: false,
        closeOnConfirm: false,
        closeOnCancel: false,
        closeOnEscape: false,
        closeOnOutsideClick: false
    });

    /** Image Tools **/

    let cropperInitialized  = false;

    let scOptions    = Joomla.getOptions('com_socialcommunity.avatar');
    let aspectRatios = {
        '1/1': 1,
        '2/3': 2/3,
        '4/3': 4/3,
        '16/9': 16/9
    };
    let aspectRatio = (scOptions.aspectRatio) ? aspectRatios[scOptions.aspectRatio] : '';

    // Set picture wrapper size.
    let $pictureWrapper = jQuery("#js-fixed-dragger-cropper");
    let $cropperImage   = jQuery("#js-cropper-img");
    let $image          = jQuery("#js-avatar-img");

    let $modalLoader    = jQuery("#js-modal-loader");

    // Prepare the token as an object.
    let token        = {};
    token[formToken] = 1;

    // Upload an image.
    let formData = jQuery.fn.extend({}, {task: 'avatar.upload', format: 'raw'}, token);
    jQuery('#js-thumb-fileupload').fileupload({
        dataType: 'text json',
        formData: formData,
        singleFileUploads: true,
        send: function() {
            $uploadLoader.show();
        },
        fail: function() {
            $uploadLoader.hide();
        },
        done: function (event, response) {

            if(!response.result.success) {
                Prism.message.show(response.result.message);
            } else {

                if (cropperInitialized) {
                    $cropperImage.cropper("replace", response.result.data.url);
                } else {
                    $cropperImage.attr("src", response.result.data.url);

                    // Calculate Wrapper Size.
                    let wrapper = calculateWrapperSize(response.result.data);

                    $cropperImage.cropper({
                        viewMode: 3,
                        aspectRatio: aspectRatio,
                        autoCropArea: 0.6, // Center 60%
                        multiple: false,
                        dragCrop: false,
                        dashed: false,
                        movable: false,
                        resizable: true,
                        zoomable: false,
                        minContainerWidth: wrapper.width,
                        minContainerHeight: wrapper.height,
                        built: function() {
                            cropperInitialized = true;
                        }
                    });

                    changeCropperSize(wrapper);
                }

                $modal.open();
            }

            // Hide ajax loader.
            $uploadLoader.hide();
        }
    });

    // Initialize the button that crops the image.
    jQuery("#js-crop-btn-cancel").on("click", function() {

        // Prepare fields.
        let fields = jQuery.fn.extend({}, {task: 'avatar.cancelImageCrop', 'format': 'raw'}, token);

        jQuery.ajax({
            url: "index.php?option=com_socialcommunity",
            type: "POST",
            data: fields,
            dataType: "text json",
            beforeSend : function() {
                $modalLoader.show();
            }
        }).done(function(){
            $modalLoader.hide();
            $modal.close();
        });
    });

    jQuery("#js-crop-btn").on("click", function(event) {
        let croppedData = $cropperImage.cropper("getData");

        // Prepare data.
        let data = {
            width: Math.round(croppedData.width),
            height: Math.round(croppedData.height),
            x: Math.round(croppedData.x),
            y: Math.round(croppedData.y)
        };

        // Prepare fields.
        let fields = jQuery.fn.extend({task: 'avatar.cropImage', format: 'raw'}, data, token);

        jQuery.ajax({
            url: "index.php?option=com_socialcommunity",
            type: "POST",
            data: fields,
            dataType: "text json",
            beforeSend : function() {
                $modalLoader.show();
            }

        }).done(function(response) {

            if(response.success) {
                $modalLoader.hide();
                $modal.close();

                $image.attr("src", response.data.src);

                // Display the button "Remove Image".
                $btnImageRemove.show();
            } else {
                Prism.message.show(response.message);
            }
        });
    });

    // Initialize the button that deletes an image.
    $btnImageRemove.on('click', function(event){
        event.preventDefault();

        swal({
            text: Joomla.JText._('COM_SOCIALCOMMUNITY_QUESTION_REMOVE_IMAGE'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: Joomla.JText._('COM_SOCIALCOMMUNITY_YES_DELETE_IT'),
            cancelButtonColor: '#d33',
            cancelButtonText: Joomla.JText._('COM_SOCIALCOMMUNITY_CANCEL')
        }).then(function () {
            let formData = jQuery.fn.extend({}, {task: 'avatar.removeImage', format: 'raw'}, token);

            jQuery.ajax({
                url: "index.php?option=com_socialcommunity",
                type: "POST",
                data: formData,
                dataType: "text json",
                beforeSend: function () {
                    $uploadLoader.show();
                }
            }).done(function (response) {
                $uploadLoader.hide();
                $image.attr('src', response.data.url);
                $btnImageRemove.hide();
                Prism.message.show(response.message);
            });
        });
    });

    function calculateWrapperSize(fileData) {
        let imageWidth    = parseInt(fileData.width);
        let imageHeight   = parseInt(fileData.height);

        let wrapper = {
            width: imageWidth,
            height: imageHeight
        };

        if (imageWidth > 600) {
            let x = (imageWidth/600).toFixed(3);
            wrapper.width = Math.round(imageWidth / x);
        }

        if (imageHeight > 400) {
            let y = (imageHeight/400).toFixed(3);
            wrapper.height = Math.round(imageHeight / y);
        }

        return wrapper;
    }

    function changeCropperSize(wrapper) {
        $pictureWrapper.css({
            width: wrapper.width,
            height: wrapper.height
        });
    }

});