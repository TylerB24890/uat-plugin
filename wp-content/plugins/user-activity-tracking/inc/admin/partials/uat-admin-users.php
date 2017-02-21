<div class="wrap">
	<h1><?php _e('User Activity Tracking', UAT_SLUG); ?></h1>
	<h2 class="subhead"><?php _e('Users', UAT_SLUG); ?></h2>

	<?php
	if(!isset($_GET['user']) && !isset($_GET['org'])) {

		$uat_users_list = new UAT_Admin_Users_List();
		$uat_users_list->uat_users_list();
		$uat_users_list->display();

	} elseif(isset($_GET['user'])) {

		$uat_users = new UAT_Users();
		$user = $uat_users->uat_get_user_data($_GET['user']);

		print_r($user);

	}
	?>
</div>
