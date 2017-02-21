<?php

/**
 * User Activity Tracking Database Management
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Database_Management')) :

	class UAT_Database_Management extends UAT_Database_Config {

		/**
		 * Executed on class istantiation.
		 *
		 * Constructs parent object
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Insert user into database
		 *
		 * @param	 $vals - array - validated array of user data
		 * @return	 string - cookie value
		 * @since    1.0.0
		 */
		public function uat_insert_user($vals) {

			$vals['reg_date'] = current_time('mysql');
			$user = $this->uat_get_user_by(null, $vals['user_email']);

			if(!$user) {
				if(!$this->wpdb->insert($this->user_table, $vals)) {
					$return['status'] = '0';
					$return['msg'] = __('There was an error saving your information. Please try again.', UAT_SLUG);
					$return['type'] = 'general';
					$this->_ajax_resp($return);
				} else {
					return $this->wpdb->insert_id;
				}
			} else {
				$uobj = new UAT_Users();
				$uobj->uat_set_user_cookie(null, null, $user->cookie_val);
				return $user->id;
			}
		}

		/**
		 * Delete user from database
		 *
		 * @return	 bool
		 * @since    1.0.0
		 */
		public function uat_delete_user($user_id) {

		}

		/**
		 * Retrieve user from database
		 *
		 * Also used to check if a user or cookie exists in database
		 *
		 * @param	 $id - int - user ID
		 * @param 	 $email - string - user email
		 * @param	 $cookie - string - cookie stored on user computer
		 * @return	 array - [user_id, email, cookie_val] || FALSE - no user found
		 * @since    1.0.0
		 */
		public function uat_get_user_by($id = null, $email = null, $cookie = null) {
			if($id !== null) {
				$id = intval($id);
				$query = "SELECT * FROM $this->user_table WHERE id = '$id'";
				$user = $this->wpdb->get_row($query);
				return $user;

			} elseif ($email !== null) {

				$query = "SELECT * FROM $this->user_table WHERE user_email = '$email'";
				$user = $this->wpdb->get_row($query);
				return $user;

			} elseif($cookie !== null) {

				$query = "SELECT * FROM $this->user_table WHERE cookie_val = '$cookie'";
				$user = $this->wpdb->get_row($query);
				return $user;
			}

			return false;
		}

		/**
		 * Insert download record into DB
		 *
		 * @param	 $user_id - int - user ID
		 * @param 	 $doc_id - int - document downloaded
		 * @param 	 $post_id - int - ID of the parent post
		 * @return	 bool
		 * @since    1.0.0
		 */
		public function uat_insert_download_record($vals) {
			$vals['download_date'] = current_time('mysql');

			if(!$this->wpdb->insert($this->download_table, $vals)) {
				$return['status'] = '0';
				$return['msg'] = __('Error saving download record.', UAT_SLUG);
				$return['type'] = 'general';
				$this->_ajax_resp($return);
			} else {
				return true;
			}
		}

		/**
		* Get downloads by given parameters
		*
		* @param	 $type - string [file, type, post, user]
		* @param	 $val - string || int
		* @return	 obj
		* @since     1.0.0
		*/
		public function uat_get_downloads_by($type = 'all', $val = null) {
			switch($type) {
				case 'all' :
					// get all downloads
					$query = "SELECT * FROM $this->download_table";
				break;
				case 'type' :
					$query = "SELECT * FROM $this->download_table WHERE doc_type = '$val'";
				break;
				case 'post' :
					if(!is_numeric($val)) {
						$val = url_to_postid($val);
					}
					$query = "SELECT * FROM $this->download_table WHERE doc_post = $val";
				break;
				case 'user' :
					$query = "SELECT * FROM $this->download_table WHERE user_id = $val";
				break;
				case 'custom' :
					$query = $val;
				break;
			}

			$downloads = $this->wpdb->get_results($query);

			return $downloads;
		}

		/**
		 * Executes necessary AJAX response for WP
		 *
		 * @param	 $resp - array - ajax response to send to browser
		 * @since    1.0.0
		 */
		public function uat_get_all_users($count = false) {
			if($count) {
				$query = "SELECT COUNT(*) FROM $this->user_table";
				$users = $this->wpdb->get_var($query);
			} else {
				$query = "SELECT * FROM $this->user_table";
				$users = $this->wpdb->get_results($query);
			}

			return $users;
		}

		/**
		 * Executes necessary AJAX response for WP
		 *
		 * @param	 $resp - array - ajax response to send to browser
		 * @since    1.0.0
		 */
		public function _ajax_resp($resp) {
			echo json_encode($resp);
			die();
		}
	}

	new UAT_Database_Management();

endif;