/**
 * Script to handle the user login form submission
 *
 * @author	Tyler Bailey
 * @package	uat/inc/frontend
 */

(function($) {
	'use strict';

	var uat_cookie = Cookies.get('uat_logged');

	if(typeof uat_cookie === 'undefined' || uat_cookie.length < 1) {
		// Add the modal launch attributes to download links
	    $('a').each(function() {
			if($(this).data('type')) {
				$(this).attr('data-toggle', 'modal');
				$(this).attr('data-target', '#uat-modal');
			}
	    });
	}

	// On login form submission
	$('form#uat-login').on('submit', function(e) {
		e.preventDefault();
		var userData = $(this).serialize();
		var msgCont = $(this).find('#msg > span');

		var dataType = $(this).find('input#download-type').val();
		var dataLink = $(this).find('input#download-url').val();

		// Ajax request
		$.ajax({
			url: uat.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: { 'action': 'user_activity_login', 'userData': userData },
			success: function(resp) {
				$('form#uat-login').each(function() {
					this.reset();
				});
				$('form#uat-login')[0].reset();

				// If there was a PHP error returned
				if(resp.status == '0') {
					// Add the 'has-error' class to the parent form group to indicate
					// an error has occurred with this field
					var formGroup = $('#' + resp.type).parents('.form-group');
					formGroup.addClass('has-error');

					// Display error message
					msgCont.addClass('error');
					msgCont.text(resp.message);
				} else {
					$('#uat-modal').modal('hide');
					$('a').each(function() {
						$(this).removeAttr('data-toggle');
						$(this).removeAttr('data-target');
				    });

					Cookies.set('uat_logged', resp.user_cookie, { expires: 3650, path: '/' });

					$('body').addClass('uat-logged');

					window.location.href = resp.url;
				}
			}
		});
	});

})(jQuery);
