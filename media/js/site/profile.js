jQuery(document).ready(function() {

	// Initialize symbol length indicator
	jQuery('#sc-wall-textarea').maxlength({
		allowOverMax: true,
		appendToParent: true,
		showOnReady: true,
		customMaxAttribute: 140
	});

    Vue.component('wallpost', {
        template: `
<div class="list-group-item mb-10" >
    <div class="row">
        <div class="col-md-1">
            <a v-bind:href="profile.link" rel="nofollow">
                <img v-bind:src="profile.image" v-bind:alt="profile.image_alt" />
            </a>
        </div>

        <div class="col-md-10">
            <h4 class="list-group-item-heading">
                <a  v-bind:href="profile.link" rel="nofollow">
                    {{profile.name}}
                </a>
                &nbsp;
                <span class="sc-user-alias">@{{profile.alias}}</span>
                &nbsp;
                <span class="sc-post-created">{{post.created_at}}</span>
            </h4>
            <p class="list-group-item-text">{{post.content}}</p>
        </div>
        
        <div class="col-md-1">
        <div class="dropdown">
          <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span class="fa fa-ellipsis-h"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li>
                <a href="javascript:void(0);" v-on:click.prevent="removePost(post.id)">
                    {{action_delete}}
                </a>
            </li>
            <li><a href="javascript:void(0);" v-on:click.prevent="editPost(post.id)">{{action_edit}}</a></li>
          </ul>
        </div>
        </div>
    </div>
    <!--<div class="sc-user-post-footbar">
        <div class="row">
            <div class="col-md-12">
                <a href="#">
                    Like
                </a>
            </div>
        </div>
    </div>-->
</div>`,
        props: ['post', 'profile'],
        data: function (){
            return {
                action_delete: Joomla.JText._('COM_SOCIALCOMMUNITY_DELETE'),
                action_edit: Joomla.JText._('COM_SOCIALCOMMUNITY_EDIT')
            }
        },
        methods: {
            removePost(id) {

                if (confirm(Joomla.JText._('COM_SOCIALCOMMUNITY_QUESTION_REMOVE_POST'))) {
                    let formData = jQuery.fn.extend({}, {task: 'profile.removePost', 'id': parseInt(id)}, requestData);

                    jQuery.ajax({
                        type: "POST",
                        url: "index.php",
                        dataType: "text json",
                        data: formData
                    }).done(function (response) {
                        if (response.success) {
                            let index = vueWallPosts.posts.findIndex(function (element) {
                                return parseInt(element.id) === parseInt(id);
                            });

                            vueWallPosts.posts.splice(index, 1);
                            Prism.message.show(response.message);
                        }
                    });
                }

            },
            editPost(id) {
                let post = vueWallPosts.posts.find(function(element){
                    return parseInt(element.id) === parseInt(id);
                });

                scOptions.editedPostId = parseInt(post.id);

                jQuery('#js-post-editor').val(post.content);
                $modalEditPost.iziModal('open');
            }
        }
    });

    // Get the options that come from server backend.
    let scOptions    = Joomla.getOptions('com_socialcommunity.profile');

    // Add an option that will contain the ID of edited post.
    scOptions.editedPostId = 0;

    // Prepare the default data of the requests.
    let requestData = {
        option: 'com_socialcommunity',
        format: 'raw'
    };
    requestData[scOptions.token] = 1;

    let vueWallPosts = new Vue({
        el: '#js-sc-wall-posts',
        data: {
            posts: []
        },
        beforeMount: function() {

            let thisVue  = this;
            let formData = jQuery.fn.extend({}, {task: 'profile.posts'}, requestData);

            jQuery.ajax({
                type: "GET",
                url: "index.php",
                dataType: "text json",
                data: formData
            }).done(function(response) {
                if (response.success){
                    thisVue.profile = response.data.profile;
                    thisVue.posts   = response.data.posts;
                }
            });
        }
    });

	jQuery("#js-sc-wall-form").on("submit", function(event){
		event.preventDefault();

		// Check the length of the text.
		let $textArea = jQuery('#sc-wall-textarea');
		if ($textArea.hasClass('overmax')) {
            let message = {
                title: Joomla.JText._('COM_SOCIALCOMMUNITY_WARNING'),
                content: Joomla.JText._('COM_SOCIALCOMMUNITY_LENGTH_POST_D').replace('%d', '140'),
                type: 'warning'
            };

			Prism.message.show(message);
			return false;
		}

        let contentValue = $textArea.val().trim();

        // Do not post empty string.
        if (!contentValue) {
            return false;
        }

        let formData = jQuery.fn.extend({}, {task: 'profile.storePost', content: contentValue}, requestData);
		jQuery.ajax({
			type: "POST",
			url: "index.php",
			dataType: "text json",
			data: formData
		}).done(function(response) {

		    // Add the new element to Vue data.
            if (response !== '' && response.success){
                vueWallPosts.posts.unshift(response.data.post);
                $textArea.val('');
            }
		});
	});

	// Initialize modal window for editing a post.
    let $modalEditPost    = jQuery('#js-edit-post-modal');
    $modalEditPost.iziModal();

    // Initialize button Cancel
    jQuery('#js-edit-post-btn-cancel').on('click', function() {
        jQuery('#js-post-editor').val('');
        $modalEditPost.iziModal('close');
    });

    // Initialize button Submit
    jQuery('#js-edit-post-btn-submit').on('click', function() {
        let $postEditTextarea = jQuery('#js-post-editor');
        let contentValue      = $postEditTextarea.val().trim();

        // Do not post empty string.
        if (!contentValue) {
            return false;
        }

        // Prepare the request options.
        let requestOptions = {
            task: 'profile.updatePost',
            id: parseInt(scOptions.editedPostId),
            content: contentValue
        };

        let formData = jQuery.fn.extend({}, requestOptions, requestData);
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            dataType: "text json",
            data: formData
        }).done(function(response) {

            // Update the value of the post in Vue data.
            if (response !== '' && response.success){
                let post = vueWallPosts.posts.find(function(element){
                    return parseInt(element.id) === parseInt(scOptions.editedPostId);
                });

                post.content = $postEditTextarea.val();
            }

            // Show the message.
            Prism.message.show(response.message);

            // Reset the values.
            $postEditTextarea.val('');
            scOptions.editedPostId = 0;

            // Close the modal window.
            $modalEditPost.iziModal('close');
        });

    });

});
