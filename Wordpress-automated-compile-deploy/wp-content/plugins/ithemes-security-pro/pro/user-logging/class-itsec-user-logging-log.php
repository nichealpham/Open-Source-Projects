<?php

/**
 * Log 404 errors for Intrusion Detection Module
 *
 * @package    iThemes-Security
 * @subpackage Intrusion-Detection
 * @since      4.0
 */
final class ITSEC_User_Logging_Log extends ITSEC_WP_List_Table {

	function __construct() {

		parent::__construct(
			array(
				'singular' => 'itsec_user_logging_log_item',
				'plural'   => 'itsec_user_logging_log_items',
				'ajax'     => true
			)
		);

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
		if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
		}

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
	 * Define first time column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_post_id( $item ) {

		$post   = get_post( $item['post_id'], 'ARRAY_A' );
		$output = '';

		if ( $post !== null ) {

			$output = '<strong>' . __( 'Title', 'it-l10n-ithemes-security-pro' ) . ':</strong> ' . $post['post_title'] . '</br>';
			$output .= '<strong>' . __( 'Type', 'it-l10n-ithemes-security-pro' ) . ':</strong> ' . $post['post_type'] . '</br>';
			$output .= '<a href="' . get_edit_post_link( $item['post_id'] ) . '">' . __( 'Edit Content', 'it-l10n-ithemes-security-pro' ) . '</a>';

		}

		return $output;

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
	 * Define count column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_user( $item ) {

		$user = get_user_by( 'login', $item['user'] );

		if ( $user !== false ) {

			return '<a href="/wp-admin/user-edit.php?user_id=' . $user->ID . '">' . $item['user'] . '</a>';

		} else {

			return $item['user'];

		}

	}

	/**
	 * Define uri column
	 *
	 * @param array $item array of row data
	 *
	 * @return string formatted output
	 *
	 **/
	function column_action( $item ) {

		return $item['action'];

	}

	/**
	 * Define Columns
	 *
	 * @return array array of column titles
	 */
	public function get_columns() {

		return array(
			'user'    => __( 'User', 'it-l10n-ithemes-security-pro' ),
			'action'  => __( 'Action', 'it-l10n-ithemes-security-pro' ),
			'post_id' => __( 'Post', 'it-l10n-ithemes-security-pro' ),
			'time'    => __( 'Time', 'it-l10n-ithemes-security-pro' ),
			'host'    => __( 'IP Address', 'it-l10n-ithemes-security-pro' ),
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
			'user'    => array( 'user', $order ),
			'action'  => array( 'action', $order ),
			'post_id' => array( 'post_id', $order ),
			'time'    => array( 'time', $order ),
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

		$items = $itsec_logger->get_events( 'user_logging' );

		$table_data = array();

		$count = 0;

		foreach ( $items as $item ) { //loop through and group 404s

			$log_data = maybe_unserialize( $item['log_data'] );

			$table_data[$count]['time'] = sanitize_text_field( $item['log_date'] );
			$table_data[$count]['host'] = sanitize_text_field( $item['log_host'] );

			if ( strlen( trim( sanitize_text_field( $item['log_username'] ) ) ) > 0 ) {

				$table_data[$count]['user'] = sanitize_text_field( $item['log_username'] );

			} elseif ( intval( $item['log_user'] ) > 0 && ITSEC_Lib::user_id_exists( $item['log_user'] ) ) {

				$user = get_user_by( 'id', $item['log_user'] );

				$table_data[$count]['user'] = $user->data->user_login;

			} else {

				$table_data[$count]['user'] = '';

			}

			$table_data[$count]['action'] = sanitize_text_field( $log_data['action'] );

			if ( isset( $log_data['post'] ) ) {

				$table_data[$count]['post_id'] = sanitize_text_field( $log_data['post'] );

			} else {

				$table_data[$count]['post_id'] = '';

			}

			$count ++;

		}

		usort( $table_data, array( $this, 'sortrows' ) );

		$per_page     = 50; //20 items per page
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

		if ( $orderby == 'count' ) {

			if ( intval( $a[$orderby] ) < intval( $b[$orderby] ) ) {
				$result = - 1;
			} elseif ( intval( $a[$orderby] ) === intval( $b[$orderby] ) ) {
				$result = 0;
			} else {
				$result = 1;
			}

		} else {

			// Determine sort order
			$result = strcmp( $a[$orderby], $b[$orderby] );

		}

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;

	}

}
