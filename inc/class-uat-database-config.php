<?php

/**
 * User Activity Tracking Database Configuration
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Database_Config')) :

	class UAT_Database_Config {

		/**
	     * WPDB Object
	     *
	     * @since 1.0.0
	     *
	     * @type object
	     */
		public $wpdb;

		/**
	     * WPDB Charset
	     *
	     * @since 1.0.0
	     *
	     * @type string
	     */
		public $wpdb_charset;

		/**
	     * UAT User Table Name
	     *
	     * @since 1.0.0
	     *
	     * @type string
	     */
		public $user_table;

		/**
	     * UAT Download Table
	     *
	     * @since 1.0.0
	     *
	     * @type string
	     */
		public $download_table;

		/**
		 * Executed on class istantiation.
		 *
		 * Sets global WPDB object variable
		 * Sets plugin table names
		 *
		 * Executes table creation if not exist
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			// Global WPDB object
			global $wpdb;
			$this->wpdb = $wpdb;

			// WPDB Charset
			$this->wpdb_charset = $this->wpdb->get_charset_collate();

			// Users table name
			$this->user_table = $this->wpdb->prefix . 'uat_users';

			// Downloads table name
			$this->download_table = $this->wpdb->prefix . 'uat_downloads';
		}

		/**
		 * Checks if the DB tables exist and executes the creation function if not
		 *
		 * @since    1.0.0
		 */
		public function check_db_tables() {
			// Check for a users table
			if($this->wpdb->get_var("SHOW TABLES LIKE '" . $this->user_table . "'") != $this->user_table) {
				$this->_create_user_db_table();
			}

			// Check for a downloads table
			if($this->wpdb->get_var("SHOW TABLES LIKE '" . $this->download_table . "'") != $this->download_table) {
				$this->_create_download_db_table();
			}
		}

		/**
		 * Creates the users database table
		 *
		 * @since    1.0.0
		 */
		private function _create_user_db_table() {
			// Users table structure
			$sql = "CREATE TABLE $this->user_table (
			  	id mediumint(9) NOT NULL AUTO_INCREMENT,
			  	reg_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  	first_name text NOT NULL,
				last_name text NOT NULL,
			  	user_email varchar(55) NOT NULL,
			  	user_org text DEFAULT '' NOT NULL,
			  	cookie_val text DEFAULT '' NOT NULL,
			  	PRIMARY KEY  (id)
		  ) $this->wpdb_charset;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// Execute sql statement
			dbDelta( $sql );
		}

		/**
		 * Creates the download tracking database table
		 *
		 * @since    1.0.0
		 */
		private function _create_download_db_table() {
			// Downloads table structure
			$sql = "CREATE TABLE $this->download_table (
			  	id mediumint(9) NOT NULL AUTO_INCREMENT,
			  	user_id mediumint(9) NOT NULL,
				doc_type text DEFAULT '' NOT NULL,
			  	doc_id mediumint(9) DEFAULT NULL,
				doc_post text DEFAULT '' NOT NULL,
			  	download_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  	PRIMARY KEY  (id)
		  ) $this->wpdb_charset;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// Execute sql statement
			dbDelta( $sql );
		}

		protected function uat_delete_database_tables() {
			$u_sql = "DROP TABLE IF EXISTS $this->user_table";
			$this->wpdb->query($u_sql);

			$dl_sql = "DROP TABLE IF EXISTS $this->download_table";
			$this->wpdb->query($dl_sql);
		}
	}

endif;
