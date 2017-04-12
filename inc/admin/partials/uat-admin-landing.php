<div class="wrap">
	<h1><?php _e('User Activity Tracking', UAT_SLUG); ?></h1>

	<h2 class="subhead"><?php _e('Overview', UAT_SLUG); ?></h2>

	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th>
					<?php _e('Total Downloads', UAT_SLUG); ?>
				</th>
				<th>
					<?php _e('Most Popular Post', UAT_SLUG); ?>
				</th>
				<th>
					<?php _e('Most Active User', UAT_SLUG); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo do_shortcode('[uat_admin_counts]'); ?>
				</td>
				<td>
					<?php echo do_shortcode('[uat_admin_counts post = true]'); ?>
				</td>
				<td>
					<?php echo do_shortcode('[uat_admin_counts user = true]'); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<div id="settings" style="margin-top: 30px; background: #FFF; padding: 15px;">
		<form method="POST" action="options.php">
			<?php
			settings_fields('uat_options');
			do_settings_sections('uat');
			submit_button();
			?>
		</form>
	</div>
</div>
