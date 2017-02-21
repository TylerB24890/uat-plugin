<div class="wrap">
	<h1><?php _e('User Activity Tracking', UAT_SLUG); ?></h1>

	<h2 class="subhead"><?php _e('Downloads', UAT_SLUG); ?></h2>
	<?php echo(isset($_GET['user']) || isset($_GET['pid']) || isset($_GET['type']) ? '<a href="?page=uat-downloads" class="button button-secondary">' . __('Reset Filters', UAT_SLUG) . '</a>' : ''); ?>

	<?php
	$uat_dl_list = new UAT_Admin_Downloads_List();

	if(!isset($_GET['user']) && !isset($_GET['pid']) && !isset($_GET['type'])) {
		$uat_dl_list->uat_downloads_list();
	} elseif(isset($_GET['user'])) {
		if(!is_numeric($_GET['user'])) {
			echo 'Invalid User ID.';
		} else {
			$uat_dl_list->uat_downloads_list('user', $_GET['user']);
		}

	} elseif(isset($_GET['pid']) && !isset($_GET['type'])) {
		if(!is_numeric($_GET['pid'])) {
			echo 'Invalid Post ID';
		} else {
			$uat_dl_list->uat_downloads_list('post', $_GET['pid']);
		}

	} elseif(isset($_GET['type']) && !isset($_GET['pid'])) {
		$uat_dl_list->uat_downloads_list('type', $_GET['type']);
	} else {
		if(!is_numeric($_GET['pid'])) {
			_e('Invalid Post ID', UAT_SLUG);
		} else {
			$dlm = new UAT_Downloads();
			$dltbl = $dlm->uat_return_download_table_name();
			$post_id = $_GET['pid'];
			$type = $_GET['type'];

			$query = "SELECT * FROM $dltbl WHERE doc_post = '$post_id' AND doc_type = '$type'";
			$uat_dl_list->uat_downloads_list('custom', $query);
		}
	}

	$uat_dl_list->display();
	?>
</div>

<script>
function uat_go_back() {
	window.history.back();
}
</script>
