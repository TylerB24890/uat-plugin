<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://tylerb.me
 * @since      1.0.0
 *
 * @package    uat
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
} else {
    uat_uninstall_plugin();
}

function uat_uninstall_plugin() {
    $dbobj = new UAT_Database_Management();

    $usql = "DROP TABLE IF EXISTS $dbobj->user_table";
    $dlsql = "DROP TABLE IF EXISTS $dbobj->download_table";

    $dlsql->wpdb->query($usql);
    $dlsql->wpdb->query($dlsql);
}
