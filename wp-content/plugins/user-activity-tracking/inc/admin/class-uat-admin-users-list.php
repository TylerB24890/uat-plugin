<?php

/**
* User Activity Tracking - Users List
*
* Uses WP_List_Table class to render the users list
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

if(!class_exists('UAT_Admin_Users_List')) :

    class UAT_Admin_Users_List extends WP_List_Table {

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
        * @since    1.0.0
        */
        public function uat_users_list($per_page = 10) {
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = array();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $this->uat_prepare_user_data($per_page);
        }

        /**
        * Overrides the WP_List_Table get_columns function (required)
        *
        * @since    1.0.0
        */
        public function get_columns() {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'user_name' => __('Name', UAT_SLUG),
                'user_email' => __('Email', UAT_SLUG),
                'user_org' => __('Organization', UAT_SLUG),
                'downloads' => __('# of Downloads', UAT_SLUG),
                'reg_date' => __('Date Registered', UAT_SLUG)
            );
            return $columns;
        }

        /**
        * Sets the columns to display the supplied data from uat_prepare_user_data()
        *
        * @param    $item - array - column data
        * @param    $column_name - string - column name where $item lives
        * @since    1.0.0
        */
        public function column_default($item, $column_name) {
            switch( $column_name ) {
                case 'cb':
                case 'user_name':
                case 'user_email':
                case 'user_org':
                case 'downloads':
                case 'reg_date':
                return $item[ $column_name ];
                default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
            }
        }

        /**
        * WP_List_Table function to add the bulk action checkboxes to rows
        *
        * @since    1.0.0
        */
        public function column_cb($item) {
            return sprintf('<input type="checkbox" name="user[]" value="%s" />', $item['ID']);
        }

        /**
        * Overrides the WP_List_Table get_bulk_actions function
        *
        * Adds bulk action selector
        *
        * @since    1.0.0
        */
        public function get_bulk_actions() {
            $actions = array(
                'delete' => __('Delete', UAT_SLUG),
            );

            return $actions;
        }

        /**
        * Retreives data to populate downloads table
        *
        * @since    1.0.0
        */
        public function uat_prepare_user_data($per_page) {

            $uobj = new UAT_Users();

            $users = $uobj->uat_get_all_users();

            $return_array = array();
            $display_array = array();

            foreach($users as $user => $data) {
                // User ID
                $display_array['ID'] = $data->id;
                // User Name
                $display_array['user_name'] = $data->first_name . ' ' . $data->last_name;
                // User Email
                $display_array['user_email'] = sprintf('<a href="mailto:%s">%s</a>', $data->user_email, $data->user_email);
                // User Org
                $display_array['user_org'] = $data->user_org;
                // User Downlaod Count
                $display_array['downloads'] = sprintf('<a href="?page=%s&user=%s">%s</a>', 'uat-downloads', $data->id, count($uobj->uat_get_downloads_by('user', $data->id)));
                // User Reg date
                $display_array['reg_date'] = date('F jS, Y', strtotime($data->reg_date));

                $return_array[] = $display_array;
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
                'edit'      => sprintf('<a href="?page=%s&action=%s&user=%s">' . __('Edit', UAT_SLUG) . '</a>', $_REQUEST['page'], 'edit', $item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&user=%s">' . __('Delete', UAT_SLUG) . '</a>', $_REQUEST['page'], 'delete', $item['ID']),
                'overview'    => sprintf('<a href="?page=%s&user=%s">' . __('Overview', UAT_SLUG) . '</a>', 'uat-users', $item['ID']),
            );

            return sprintf('%1$s %2$s', $item['user_name'], $this->row_actions($actions) );
        }
    }

endif;
