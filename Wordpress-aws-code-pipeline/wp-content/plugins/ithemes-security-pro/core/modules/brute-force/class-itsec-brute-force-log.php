<?php

/**
 * Log tables for Authentication Module
 *
 * @package    iThemes-Security
 * @subpackage Authentication
 * @since      4.0
 */
final class ITSEC_Brute_Force_Log extends ITSEC_WP_List_Table {

	function __construct() {

		parent::__construct(
		      array(
			      'singular' => 'itsec_brute_force_log_item',
			      'plural'   => 'itsec_brute_force_log_items',
			      'ajax'     => true
		      )
		);

	}

	/**
	 * Define time column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_time( $item ) {

		return $item['time'];

	}

	/**
	 * Define host column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_host( $item ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );

		$r = array();
		if ( ! is_array( $item['host'] ) ) {
			$item['host'] = array( $item['host'] );
		}
		foreach ( $item['host'] as $host ) {
			if ( ITSEC_Lib_IP_Tools::validate( $host ) ) {
				$r[] = '<a href="' . esc_url( ITSEC_Lib::get_trace_ip_link( $host ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $host ) . '</a>';
			}
		}
		$return = implode( '<br />', $r );

		return $return;

	}

	/**
	 * Define added column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_user( $item ) {

		return $item['user'];

	}

	/**
	 * Define Columns
	 *
	 * @return array array of column titles
	 */
	public function get_columns() {

		return array(
			'time' => __( 'Time', 'it-l10n-ithemes-security-pro' ),
			'host' => __( 'Host', 'it-l10n-ithemes-security-pro' ),
			'user' => __( 'Username', 'it-l10n-ithemes-security-pro' ),
		);

	}

	/**
	 * Define Sortable Columns
	 *
	 * @return array of column titles that can be sorted
	 */
	public function get_sortable_columns() {

		$order = ( empty( $_GET['order'] ) ) ? false : true;

		$sortable_columns = array(
			'time' => array( 'time', $order ),
			'host' => array( 'host', $order ),
			'user' => array( 'user', $order ),
		);

		return $sortable_columns;

	}

	/**
	 * Prepare data for table
	 *
	 * @return void
	 */
	public function prepare_items() {

		global $itsec_logger;

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$items = $itsec_logger->get_events( 'brute_force' );

		$table_data = array();

		$count = 0;

		foreach ( $items as $item ) { //loop through and group 404s

			$table_data[$count]['time'] = sanitize_text_field( $item['log_date'] );
			$table_data[$count]['host'] = sanitize_text_field( $item['log_host'] );
			$table_data[$count]['user'] = sanitize_text_field( $item['log_username'] );

			$count ++;

		}

		usort( $table_data, array( $this, 'sortrows' ) );

		$per_page     = 20; //20 items per page
		$current_page = $this->get_pagenum();
		$total_items  = count( $table_data );

		$table_data = array_slice( $table_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $table_data;

		$this->set_pagination_args(
		     array(
			     'total_items' => $total_items,
			     'per_page'    => $per_page,
			     'total_pages' => ceil( $total_items / $per_page )
		     )
		);

	}

	/**
	 * Sorts rows by count in descending order
	 *
	 * @param array $a first array to compare
	 * @param array $b second array to compare
	 *
	 * @return int comparison result
	 */
	function sortrows( $a, $b ) {

		// If no sort, default to count
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? esc_attr( $_GET['orderby'] ) : 'time';

		// If no order, default to desc
		$order = ( ! empty( $_GET['order'] ) ) ? esc_attr( $_GET['order'] ) : 'desc';

		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;

	}

}
