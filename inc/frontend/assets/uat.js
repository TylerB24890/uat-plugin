/**
 * Script to manage user downloads
 *
 * Runs purely on click of any link
 *
 * @author	Tyler Bailey
 * @package	uat/inc/frontend
 */

(function($) {
	'use strict';

	var uat_cookie = Cookies.get('uat_logged');

	if(typeof uat_cookie != 'undefined' && uat_cookie.length > 1) {
		if(!$('body').hasClass('uat-logged')) {
			$('body').addClass('uat-logged');
		}
	}

	// Detect link click
	$('a').on('click', function(e) {

		// If data-type attribute is found
		if($(this).attr('data-type')) {

			if(typeof uat_cookie === 'undefined' || uat_cookie.length < 1) {
				uat_cookie = Cookies.get('uat_logged');
			}

			e.preventDefault();

			var dataType = '';
			var dataPost = '';
			var dataImg = '';
			var dataLink = '';

			// Get the download data (type, parent post, [image ID])
			dataType = $(this).data('type');
			dataPost = $(this).data('post');
			dataLink = $(this).attr('href');

			if(dataType === 'image') {
				dataLink = $(this).data('url');
			}

			// If the login modal is present then populate the hidden form fields
			if($('#uat-modal').length && !$('body').hasClass('uat-logged')) {
				$('#uat-login').find('input#download-post').val(dataPost);
				$('#uat-login').find('input#download-type').val(dataType);
				$('#uat-login').find('input#download-url').val(dataLink);
			} else {
				// Ajax request to save download data to database
				$.ajax({
					url: uat.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: { 'action': 'user_activity_download', 'type': dataType, 'post': dataPost, 'link': dataLink, 'user_cookie': uat_cookie },
					success: function(resp) {

						// If there was a PHP error returned
						if(resp.status == '0') {
							alert(resp.msg);
						} else {
							window.location.href = resp.url;
						}
					}
				});
			}
		}
	});

})(jQuery);
