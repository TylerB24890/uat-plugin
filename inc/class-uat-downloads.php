<?php

/**
 * User Activity Tracking Download Manager
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Downloads')) :

	class UAT_Downloads extends UAT_Users {

		/**
		 * Executed on class istantiation.
		 *
		 * Constructs parent object
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'wp_ajax_user_activity_download', array($this, 'uat_process_download') );
			add_action( 'wp_ajax_nopriv_user_activity_download', array($this, 'uat_process_download') );
		}

		/**
	 	* Process User Download Request
	 	*
	 	* @since     1.0.0
	 	*/
	    public function uat_process_download($sqlv = array()) {
			$error = false;

			if(empty($sqlv)) {
				$cookie_val = $_POST['user_cookie'];

				if($cookie_val && strlen($cookie_val) > 3) {
					$user = $this->uat_get_user_by(null, null, strval($cookie_val));
					$user_id = $user->id;

					$file_type = $_POST['type'];
					$post_id = $_POST['post'];
					$link = $_POST['link'];

					if(strtolower($file_type) === 'image') {
						$post_table = $this->wpdb->prefix . 'posts';
						$sql = "SELECT ID FROM $post_table WHERE guid = '$link'";
						$attach = $this->wpdb->get_col($sql);
						$img = $attach[0];
					}

					if($file_type == 'image' && !is_numeric($img)) {
						$error = true;
					} elseif($file_type !== 'image') {
						$img = null;
					}

					$sqlv = array(
						'user_id' => $user_id,
						'doc_id' => $img,
						'doc_type' => $file_type,
						'doc_post' => $post_id,
					);

					if(!is_numeric($post_id)) {
						$error = true;
					}
				} else {
					$error = true;
				}
			} else {
				// Get the cookie value
				$cookie_val = $sqlv['user_cookie'];
				// Get the file link
				$link = $sqlv['link'];

				// Unset cookie and link from sql statement
				unset($sqlv['user_cookie']);
				unset($sqlv['link']);
			}

			if($error !== true && $this->uat_insert_download_record($sqlv)) {
				$return['status'] = '1';
				$return['url'] = $link;
				$return['user_cookie'] = $cookie_val;
				$this->_ajax_resp($return);
			} else {
				$error = true;
			}

			if($error) {
				$return['status'] = '0';
				$return['msg'] = __('There was an error with your download. Please try again.', UAT_SLUG);
				$this->_ajax_resp($return);
			}
	    }

		/**
	 	* Get single user download data
	 	*
	 	* @param	 $user_id - int - ID of user to retrieve
	 	* @return	 obj
	 	* @since     1.0.0
	 	*/
	    public function uat_get_user_download_data($user_id) {
	    	if(!is_numeric($user_id))
	             return false;

			$downloads = $this->uat_get_downloads_by('user', $user_id);

			return $downloads;
	    }

		/**
	 	* Returns the name of the download table with site prefix
	 	*
	 	* @return	 string
	 	* @since     1.0.0
	 	*/
		public function uat_return_download_table_name() {
			return $this->download_table;
		}
	}

	new UAT_Downloads();

endif;
