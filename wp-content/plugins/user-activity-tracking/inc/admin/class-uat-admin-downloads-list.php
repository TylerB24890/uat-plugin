<?php

/**
* User Activity Tracking - Downloads List
*
* Uses WP_List_Table class to render the downloads list
*
* @author Tyler Bailey
* @version 1.0
* @package uat
* @subpackage uat/includes/admin
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if(!class_exists('UAT_Admin_Downloads_List')) :

    class UAT_Admin_Downloads_List extends WP_List_Table {

        /**
        * Constructs the WP_List_Table object
        *
        * @since    1.0.0
        */
        public function __construct() {
            parent::__construct();
        }

        /**
        * Override WP_List_Table variables
        *
        * Compiles Table Data
        *
        * @since    1.0.0
        */
        public function uat_downloads_list($type = 'all', $val = null, $per_page = 10) {
            $columns = $this->get_columns();
            $hidden = array();
            //$sortable = $this->get_sortable_columns();\
            $sortable = array();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $this->uat_prepare_download_data($type, $val, $per_page);
        }

        /**
        * Overrides the WP_List_Table get_columns function (required)
        *
        * @since    1.0.0
        */
        public function get_columns() {
            $columns = array(
                'date' => __('Date', UAT_SLUG),
                'user_name' => __('Name', UAT_SLUG),
                'user_email' => __('Email', UAT_SLUG),
                'user_org' => __('Organization', UAT_SLUG),
                'file' => __('File', UAT_SLUG),
                'type' => __('Type', UAT_SLUG),
                'post' => __('Parent Post', UAT_SLUG)
            );
            return $columns;
        }

        /**
        * Sets the columns to display the supplied data from uat_prepare_activity_data()
        *
        * @param    $item - array - column data
        * @param    $column_name - string - column name where $item lives
        * @since    1.0.0
        */
        public function column_default($item, $column_name) {
            switch( $column_name ) {
                case 'date':
                case 'user_name':
                case 'user_email':
                case 'user_org':
                case 'file':
                case 'type':
                case 'post':
                return $item[ $column_name ];
                default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
            }
        }

        /**
        * Retreives data to populate downloads table
        *
        * @since    1.0.0
        */
        public function uat_prepare_download_data($type, $val, $per_page) {

            $dlobj = new UAT_Downloads();

            $downloads = $dlobj->uat_get_downloads_by($type, $val);

            $return_array = array();
            $display_array = array();

            foreach($downloads as $download => $data) {
                $user = $dlobj->uat_get_user_by($data->user_id);

                if(is_object($user)) {
                    $display_array['ID'] = $data->id;
                    $display_array['date'] = sprintf('%s <br /> %s', date("M j, Y", strtotime($data->download_date)), date("g:i a", strtotime($data->download_date)));
                    $display_array['user_name'] = $user->first_name . ' ' . $user->last_name;
                    $display_array['user_id'] = $user->id;
                    $display_array['user_email'] = sprintf('<a href="mailto:%s">%s</a>', $user->user_email, $user->user_email);
                    $display_array['user_org'] = $user->user_org;

                    $display_array['file'] = sprintf('<a href="%s" target="_blank">%s</a>', wp_get_attachment_url($data->doc_id), get_the_title($data->doc_id));

                    $display_array['type'] = sprintf('<a href="?page=%s&type=%s">%s</a>', 'uat-downloads', strtolower($data->doc_type), strtoupper($data->doc_type));
                    $display_array['type_raw'] = $data->doc_type;
                    $display_array['org_raw'] = strtolower($user->user_org);
                    $display_array['post'] = sprintf('<a href="%s">%s</a>', get_the_permalink($data->doc_post), get_the_title($data->doc_post));
                    $display_array['post_id'] = $data->doc_post;
                    $display_array['post_url'] = get_the_permalink($data->doc_post);

                    $return_array[] = $display_array;
                }

            }

            $cur_page = $this->get_pagenum();

            $total_items = count($return_array);

            $return_array = array_slice($return_array, (($cur_page - 1) * $per_page), $per_page);
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page
            ));

            return $return_array;
        }

        /**
        * Sets row actions in the 'user_name' column
        *
        * @param    $item - array - column data
        * @since    1.0.0
        */
        function column_user_name($item) {
            $actions = array(
                'view-user'      => sprintf('<a href="?page=%s&user=%s">' . __('Overview', UAT_SLUG) . '</a>', 'uat-users', $item['user_id']),
                'user-downloads' => sprintf('<a href="?page=%s&user=%s">' . __('Activity', UAT_SLUG) . '</a>', 'uat-downloads', $item['user_id'])
            );

            return sprintf('%1$s %2$s', $item['user_name'], $this->row_actions($actions) );
        }

        /**
        * Sets row actions in the 'file' column
        *
        * @param    $item - array - column data
        * @since    1.0.0
        */
        function column_file($item) {
            if($item['type'] == 'image' || $item['type'] == 'IMAGE') {
                $actions = array(
                    'view-file' => sprintf('<a href="?page=%s&type=%s&pid=%s&iid=%s" target="_blank">' . __('View Downloads', UAT_SLUG) . '</a>', 'uat-downloads', strtolower($item['type_raw']), $item['post_id'], $item['doc_id'])
                );
            } else {
                $actions = array(
                    'view-file' => sprintf('<a href="?page=%s&type=%s&pid=%s">' . __('View All Downloads', UAT_SLUG) . '</a>', 'uat-downloads',strtolower($item['type_raw']), $item['post_id'])
                );
            }


            return sprintf('%1$s %2$s', $item['file'], $this->row_actions($actions) );
        }

        /**
        * Sets row actions in the 'file' column
        *
        * @param    $item - array - column data
        * @since    1.0.0
        */
        function column_post($item) {
            $actions = array(
                'view-post'      => sprintf('<a href="?page=%s&pid=%s">' . __('View Downloads', UAT_SLUG) . '</a>', 'uat-downloads', $item['post_id'])
            );

            return sprintf('%1$s %2$s', $item['post'], $this->row_actions($actions) );
        }
    }

endif;
