<?php

/**
 * User Activity Tracking User Management/Login
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Users')) :

	class UAT_Users extends UAT_Database_Management {

		/**
		 * Executed on class istantiation.
		 *
		 * Constructs parent object
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'wp_ajax_user_activity_login', array($this, 'uat_process_form_submission') );
			add_action( 'wp_ajax_nopriv_user_activity_login', array($this, 'uat_process_form_submission') );
		}

		/**
		 * Function to process the AJAX form submission
		 *
		 * @since    1.0.0
		 */
		public function uat_process_form_submission() {
			$error = false;

			$vals = array();
			parse_str($_POST['userData'], $vals);

			$valid_vals = $this->_uat_validate_form_submission($vals);

			if(is_array($valid_vals)) {
				// Get cookie value
				$valid_vals['cookie_val'] = $this->uat_set_user_cookie($valid_vals['user_email'], $valid_vals['first_name'] . ' ' . $valid_vals['last_name']);

				$user_id = $this->uat_insert_user($valid_vals);
				if(is_numeric($user_id)) {
					if($vals['download-img'] == 0 || $vals['download-img'] === null) {
						$vals['download-img'] = null;
					}
					$sqlv = array(
						'user_id' => $user_id,
						'doc_id' => $vals['download-img'],
						'doc_type' => $vals['download-type'],
						'doc_post' => $vals['download-post'],
						'link' => $vals['download-url'],
					);

					$dlobj = new UAT_Downloads();
					$dlobj->uat_process_download($sqlv);

				} else {
					$error = true;
				}
			} else {
				$error = true;
			}

			if($error) {
				$return['status'] = '0';
				$return['msg'] = __('There was an error with your submission. Please try again.', UAT_SLUG);
				$this->_ajax_resp($return);
			}

		}

		/**
		 * Display single user data
		 *
		 * @param	 $user_id - int - ID of user to retrieve
		 * @return	 obj
		 * @since    1.0.0
		 */
	     public function uat_get_user_data($user_id) {
	         if(!is_numeric($user_id))
	            return false;

	        $user = $this->uat_get_user_by($user_id);

			if(!$user || !is_array($user)) {
				echo '<h3>' . __('That user ID could not be found in our database.', UAT_SLUG) . '</h3>';
				echo '<a href="?page=uat-users">&laquo; ' . __('Return to Users', UAT_SLUG) . '</a>';
			} else {
				return $user;
			}
	     }

		/**
		 * Used to validate the user input before saving to DB
		 *
		 * @param	 $vals - array - user input data
		 * @since    1.0.0
		 */
		private function _uat_validate_form_submission($vals) {

			// Validate nonce field
			if(!isset($vals['uat_nonce']) || !wp_verify_nonce($vals['uat_nonce'], 'uat_login')) {
				$return['status'] = '0';
				$return['msg'] = __('Your form submission was not processed. Please try again.', UAT_SLUG);
				$return['type'] = 'general';
				$this->_ajax_resp($return);
			}

			// Validate Spam Honeypot
			if(strlen($vals['email2']) > 1) {
				$return['status'] = '0';
				$return['msg'] = __('You have filled out the spam field!', UAT_SLUG);
				$return['type'] = 'general';
				$this->_ajax_resp($return);
			}

			// Validate/Sanitize First Name field
			if(!isset($vals['first_name']) || strlen($vals['first_name']) < 1) {
				$return['status'] = '0';
				$return['msg'] = __('Please enter your first name.', UAT_SLUG);
				$return['type'] = 'first_name';
				$this->_ajax_resp($return);
			} else {
				$return_vals['first_name'] = sanitize_text_field($vals['first_name']);
			}

			// Validate/Sanitize Last Name field
			if(!isset($vals['last_name']) || strlen($vals['last_name']) < 1) {
				$return['status'] = '0';
				$return['msg'] = __('Please enter your last name.', UAT_SLUG);
				$return['type'] = 'last_name';
				$this->_ajax_resp($return);
			} else {
				$return_vals['last_name'] = sanitize_text_field($vals['last_name']);
			}

			// Validate/Sanitize Organization
			if(!isset($vals['user_org']) || strlen($vals['user_org']) < 1) {
				$return['status'] = '0';
				$return['msg'] = __('Please enter your organization.', UAT_SLUG);
				$return['type'] = 'org';
				$this->_ajax_resp($return);
			} else {
				$return_vals['user_org'] = sanitize_text_field($vals['user_org']);
			}

			// Validate/Sanitize Email
			if(!isset($vals['user_email']) || strlen($vals['user_email']) < 1) {
				$return['status'] = '0';
				$return['msg'] = __('Please enter your email address.', UAT_SLUG);
				$return['type'] = 'email';
				$this->_ajax_resp($return);
			} elseif(!is_email($vals['user_email'])) {
				$return['status'] = '0';
				$return['msg'] = __('You have entered an invalid email address.', UAT_SLUG);
				$return['type'] = 'email';
				$this->_ajax_resp($return);
			} else {
				$return_vals['user_email'] = sanitize_email($vals['user_email']);
			}

			// Validate agreement
			if(!isset($vals['agree']) || $vals['agree'] !== '1') {
				$return['status'] = '0';
				$return['msg'] = __('You must agree to our license agreement before continuing.', UAT_SLUG);
				$return['type'] = 'agree';
				$this->_ajax_resp($return);
			}

			return $return_vals;
		}

		/**
		 * Checks the users system for a stored cookie and returns the cookie value
		 *
		 * @since    1.0.0
		 */
		public function uat_check_user_cookie() {
			return (isset($_COOKIE[UAT_COOKIE]) ? $_COOKIE[UAT_COOKIE] : false);
		}

		/**
		 * Sets the generated cookie value on the users computer
		 *
		 * @param	 $user_email - string - The user email address
		 * @param 	 $user_name - string - Name of user
		 * @return	 string - unique cookie value
		 * @since    1.0.0
		 */
		protected function uat_set_user_cookie($email = null, $name = null, $cookie_val = null) {
			if($cookie_val === null) {
				$cookie_val = $this->_uat_generate_cookie($email, $name);
			}

			setcookie(UAT_COOKIE, $cookie_val, time() + (10 * 365 * 24 * 60 * 60), COOKIEPATH);
			return $cookie_val;
		}

		/**
		 * Generate user cookie
		 *
		 * @param	 $user_email - string - The user email address
		 * @param 	 $user_name - string - Name of user
		 * @return	 string - unique cookie value
		 * @since    1.0.0
		 */
		protected function _uat_generate_cookie($email, $name) {
			$cookie_val = sha1($email . $name);
			return $cookie_val;
		}
	}

	new UAT_Users();

endif;
