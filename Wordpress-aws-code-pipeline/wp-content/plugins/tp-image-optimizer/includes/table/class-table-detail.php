<?php

if (!defined('TP_IMAGE_OPTIMIZER_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * @class         IO Image Library
 * @version       1.0
 * Extend         WP_List_Table
 * Show data all user on WP Table List
 */
class TP_Image_Optimizer_List_Table extends WP_List_Table {

    /**
     * Data of table
     * @var Array 
     */
    var $data_table;
    public $total_items;

    /**
     * Declare number and detail column of table
     * @return Array
     */
    function get_columns() {
        $columns = array(
            'id'           => esc_html__('ID', 'tp-image-optimizer'),
            'image'        => esc_html__('Image', 'tp-image-optimizer'),
            'origin_size'  => esc_html__('Original Size', 'tp-image-optimizer'),
            'current_size' => esc_html__('Current Size', 'tp-image-optimizer'),
            'optimizer'    => esc_html__('Optimizer', 'tp-image-optimizer') . '<span class="faq-i faq-statistics_original" ></span>',
            'action'       => esc_html__('Action', 'tp-image-optimizer'),
        );
        return $columns;
    }

    /**
     * Prepare items before display on table
     * @global type $wpdb
     */
    function prepare_items() {
        //ie( $this->total_items);
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 15;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->data_table;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        usort($data, array($this, 'usort_reorder'));


        /*         * *********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         * ******************************************************************** */


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = $this->total_items;

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        //$data = array_slice($data, (($current_page - 1) * $per_page), $per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

    /**
     * 
     * @param type $a
     * @param type $b
     * @return int Filter for ID by value ID
     */
    function usort_reorder($a, $b) {
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
        $order   = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
        if ($a[$orderby] == $b[$orderby])
            return 0;
        if (($order === 'asc')) {
            return ($a[$orderby] < $b[$orderby]) ? -1 : 1;
        } else {
            return ($a[$orderby] < $b[$orderby]) ? 1 : -1;
        }
    }

    /**
     * Pagination
     * 
     */
    function pagination_items() {

        $per_page     = 10;
        $current_page = $this->get_pagenum();
        $total_items  = count($this->data_table);

        // only ncessary because we have sample data
        $this->found_data = array_slice($this->data_table, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page                     //WE have to determine how many items to show on a page
        ));
        $this->items = $this->found_data;
    }

    /**
     * Declare default column
     * @param array $item
     * @param string $column_name
     * @return type
     */
    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'url':
            case 'image':
            case 'status':
            case 'current_size' :
            case 'origin_size':
            case 'current_size':
            case 'optimizer':
            case 'detail':
            case 'action':
            case 'mime':

                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Declare sortable column
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', true),
        );
        return $sortable_columns;
    }

    /**
     * Add action for item
     * @param array $item
     * @return string
     */
    function column_user($item) {
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&user=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id_user']),
        );
        return sprintf('%1$s %2$s', $item['user'], $this->row_actions($actions));
    }

}
