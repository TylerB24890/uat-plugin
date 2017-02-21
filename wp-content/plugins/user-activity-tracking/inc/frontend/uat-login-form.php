<!-- Provided by User Activity Plugin -->
<div id="uat-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div id="page-header" class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h1 class="page-title"><?php _e('DYNAMIC UAT HEADER HERE', UAT_SLUG); ?></h1>
				<p>
					DYNAMIC UAT INTRO HERE
				</p>
			</div>
			<form id="uat-login" name="uat-login" method="POST" action="">
				<div class="modal-body">
					<div class="form-group">
						<div id="msg">
							<span></span>
						</div>
					</div>

					<div class="form-group">
						<label for="first_name"><?php _e('First Name', UAT_SLUG); ?></label>
						<input type="text" name="first_name" id="first_name" class="form-control" aria-labeled-by="first_name" required />
					</div>

					<div class="form-group">
						<label for="last_name"><?php _e('Last Name', UAT_SLUG); ?></label>
						<input type="text" name="last_name" id="last_name" class="form-control" aria-labeled-by="last_name" required />
					</div>

					<div class="form-group">
						<label for="org"><?php _e('Organization', UAT_SLUG); ?></label>
						<input type="text" name="user_org" id="user_org" class="form-control" aria-labeled-by="org" required />
					</div>

					<div class="form-group">
						<label for="email"><?php _e('Email', UAT_SLUG); ?></label>
						<input type="email" name="user_email" id="user_email" class="form-control" aria-labeled-by="email" required />
					</div>

					<input type="hidden" name="download-post" id="download-post" value="" />
					<input type="hidden" name="download-type" id="download-type" value="" />
					<input type="hidden" name="download-img" id="download-img" value="" />
					<input type="hidden" name="download-url" id="download-url" value="" />
					<input type="text" name="email2" id="email2" value="" autocomplete="off" />

					<?php wp_nonce_field('uat_login', 'uat_nonce'); ?>

					<div class="form-group">
						<div id="license-agreement">
							DYNAMIC LICENSE AGREEMENT HERE
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!-- IF USER CHOOSES TO ENTER LICENSE AGREEMENT AND REQUIRE ACCEPTANCE -->
					<div id="agreement">
						<div class="col-md-6 no-padding text-left">
							<label><input type="checkbox" name="agree" id="agree" value="1" style="position: relative; bottom: 2px;" required/><span for="agree"></span> <?php _e('I agree to the License Terms.', UAT_SLUG); ?></label>
						</div>
						<div class="col-md-6 no-padding text-right">
							<i class="fa fa-print"></i><a href="#" id="print-tos"><?php _e('Print License Terms', UAT_SLUG); ?></a>
						</div>
					</div>

					<div class="clear clearfix"></div>
					<!-- END LICENSE AGREEMENT CONDITIONAL -->
					
					<div class="form-group text-center">
						<input type="submit" class="btn btn-primary" value="<?php _e('Submit', UAT_SLUG); ?>" />
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
(function($) {
	$('a#print-tos').on('click', function(e) {
		e.preventDefault();

		$('<iframe>').hide().attr('src', '<?php echo UAT_GLOBAL_URL . 'inc/frontend/tos.htm'; ?>').appendTo("body");
	});
})(jQuery);
</script>
