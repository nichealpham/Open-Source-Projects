<?php

require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

final class ITSEC_Application_Passwords_List_Table extends WP_List_Table {
	public function __construct() {
		$args = array(
			'singular' => 'itsec-application-password',
			'plural'   => 'itsec-application-passwords',
		);

		parent::__construct( $args );
	}

	/**
	 * Get a list of columns.
	 *
	 * @since 0.1-dev
	 *
	 * @return array
	 */
	public function get_columns() {
		return ITSEC_Application_Passwords_Util::get_table_columns();
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 0.1-dev
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();
		$primary  = 'name';
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 0.1-dev
	 * @access protected
	 *
	 * @param object $item The current item.
	 * @param string $column_name The current column name.
	 */
	protected function column_default( $item, $column_name ) {
		return ITSEC_Application_Passwords_Util::get_table_column_entry( $item, $column_name );
	}

	/**
	 * Generates custom table navigation to prevent conflicting nonces.
	 *
	 * @since 0.1-dev
	 * @access protected
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( 'bottom' === $which ) : ?>
			<div class="alignright">
				<?php submit_button( __( 'Revoke all application passwords', 'it-l10n-ithemes-security-pro' ), 'delete', 'itsec-application-passwords-revoke-all', false ); ?>
			</div>
			<?php endif; ?>

			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @since 0.1-dev
	 *
	 * @param object $item The current item.
	 */
	public function single_row( $item ) {
		echo '<tr data-slug="' . esc_attr( ITSEC_Application_Passwords_Util::get_unique_slug( $item ) ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
}
