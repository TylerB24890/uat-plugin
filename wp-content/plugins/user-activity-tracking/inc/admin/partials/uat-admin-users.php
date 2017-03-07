<div class="wrap">
	<h1><?php _e('User Activity Tracking', UAT_SLUG); ?></h1>
	<h2 class="subhead"><?php _e('Users', UAT_SLUG); ?></h2>
	<form method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php
		$uat_users_list = new UAT_Admin_Users_List();

		if(!isset($_GET['user'])) {

			$uat_users_list->uat_users_list();
			$uat_users_list->display();

		} elseif(isset($_GET['action']) || isset($_GET['action2']) && isset($_GET['user'])) {
			$action = $_GET['action'];
			if(!isset($action) || $action == '-1') {
				$action = $_GET['action2'];
			}
			$users = array();


			switch($action) {
				case 'uat_delete' :
					if(is_numeric($_GET['user'])) {
						$users[] = $_GET['user'];
					}
				break;
				case 'uat_bulk_delete' :
					foreach($_GET['user'] as $u) {
						if(is_numeric($u)) {
							$users[] = $u;
						}
					}
				break;
			}

			$uat_users_list->uat_process_user_delete($users);

			$uat_users_list->uat_users_list();
			$uat_users_list->display();
		}
		?>
	</form>
</div>
