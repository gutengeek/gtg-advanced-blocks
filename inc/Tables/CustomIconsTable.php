<?php
namespace Gtg_Advanced_Blocks\Tables;

use Gtg_Advanced_Blocks\Modules\CustomIcons;

defined( 'ABSPATH' ) || exit();

class CustomIconsTable extends \WP_List_Table {

	public function get_sortable_columns() {
		$sortable_columns = [
			'title' => [ 'booktitle', false ],
		];

		return $sortable_columns;
	}

	/**
	 * bulk actions
	 */
	public function get_bulk_actions() {
		$status = $this->get_current_status();
		if ( $status === 'publish' || ! $status ) {
			$actions = [
				'trash' => __( 'Trash', 'gutengeek' ),
			];
		} elseif ( $status === 'trash' ) {
			$actions = [
				'restore' => __( 'Restore', 'gutengeek' ),
				'delete'  => __( 'Delete', 'gutengeek' ),
			];
		}

		return $actions;
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="icons[]" value="%1$s" />', absint( $item['icon_id'] ) );
	}

	/**
	 * print column name
	 */
	public function column_title( $item ) {
		$actions = [
			'edit'  => '<a href="' . admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $item['icon_id'] ) . '&action=edit">' . __( 'Edit', 'gutengeek' ) . '</a>',
			'trash' => '<a href="' . admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $item['icon_id'] ) . '&action=trash&nonce=' . wp_create_nonce( 'gutengeek-nonce-trash-icon' ) . '">' .
			           __( 'Trash',
				           'gutengeek' ) . '</a>',
		];

		$icon_status = ! empty( $item['icon_status'] ) ? sanitize_text_field( $item['icon_status'] ) : '';
		if ( $icon_status === 'trash' ) {
			$actions = [
				'restore' => '<a href="' . admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $item['icon_id'] ) . '&action=restore&nonce=' . wp_create_nonce( 'gutengeek-nonce-restore-icon' )
				             . '">' . __( 'Restore',
						'gutengeek' ) . '</a>',
				'delete'  => '<a href="' . admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $item['icon_id'] ) . '&action=delete&nonce=' . wp_create_nonce( 'gutengeek-nonce-delete-icon' ) .
				             '" class="submitdelete">' . __( 'Delete',
						'gutengeek' ) . '</a>',
			];
		}

		$state = $item['icon_status'] === 'trash' ? ' -- <span class="post-state" style="font-size: 11px; color: #444; font-weight: bold">' . __( 'trashed', 'wpaopblocks' ) . '</span>' : '';

		return sprintf(
			'%1$s %3$s',
			sprintf( '<a href="%s"><b>%s</b>%s</a>', admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $item['icon_id'] ), $item['icon_name'], $state ),
			$item['icon_id'],
			$this->row_actions( $actions )
		);
	}

	public function column_type( $item ) {
		$options       = get_option( CustomIcons::OPTION_NAME, [] );
		$icon_dir_name = $item['icon_dir_name'];
		if ( ! isset( $options[ $icon_dir_name ] ) ) {
			return '';
		}

		return esc_html( $options[ $icon_dir_name ]['custom_icon_type'] );
	}

	public function column_count( $item ) {
		$options       = get_option( CustomIcons::OPTION_NAME, [] );
		$icon_dir_name = $item['icon_dir_name'];
		if ( ! isset( $options[ $icon_dir_name ] ) ) {
			return '';
		}

		return esc_html( $options[ $icon_dir_name ]['count'] );
	}

	/**
	 * process bulk action
	 */
	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			global $wpdb;
			$icon_ids = ! empty( $_REQUEST['icons'] ) ? array_map( 'absint', $_REQUEST['icons'] ) : [];
			if ( ! empty( $icon_ids ) ) {
				foreach ( $icon_ids as $icon_id ) {
					$wpdb->update(
						$wpdb->prefix . 'gtg_block_icons',
						[ 'icon_status' => 'trash' ],
						[ 'icon_id', '=', $icon_id ]
					);
				}
			}

		}
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No icons set.', 'gutengeek' );
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'    => '<input type="checkbox" />',
			'title' => __( 'Icon Name', 'gutengeek' ),
			'type'  => __( 'Type', 'gutengeek' ),
			'count' => __( 'Count', 'gutengeek' ),
		];
	}

	/**
	 * get items
	 */
	public function get_items( $current_page = 1, $per_page = 10, $search = '', $status = '' ) {
		global $wpdb;
		$offset = ( $current_page - 1 ) * $per_page;

		$where = '';
		if ( $search ) {
			$where .= $wpdb->prepare( " AND name LIKE '%%s%'", $search );
		}
		if ( $status ) {
			$where .= $wpdb->prepare( " AND icon_status = %s", $status );
		} else {
			$where .= $wpdb->prepare( " AND icon_status = %s", 'publish' );
		}
		$order = $wpdb->prepare( 'ORDER BY icon_id DESC LIMIT %d OFFSET %d;', $per_page, $offset );

		$query = "SELECT SQL_CALC_FOUND_ROWS icon_id, icon_name, icon_dir_name, icon_user_id, icon_status FROM {$wpdb->prefix}gtg_block_icons WHERE 1 = 1 {$where} {$order}";
		$items = $wpdb->get_results( $query, ARRAY_A );

		return [
			'items'       => $items,
			'total_items' => $items ? $wpdb->get_var( 'SELECT FOUND_ROWS()' ) : 0,
		];
	}

	/**
	 * hidden columns
	 */
	public function get_hidden_columns() {
		return [
			'icon_id',
		];
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$per_page     = $this->get_items_per_page( 'gutengeek_item_per_page', 10 );
		$current_page = $this->get_pagenum();
		$search       = ! empty( $_REQUEST['s'] ) ? sanitize_text_field( ! empty( $_REQUEST['s'] ) ) : '';
		$status       = ! empty( $_REQUEST['icon_status'] ) ? sanitize_text_field( $_REQUEST['icon_status'] ) : '';
		// only ncessary because we have sample data
		$data              = $this->get_items( $current_page, $per_page, $search, $status );
		$this->items       = $data['items'];
		$this->total_items = $data['total_items'];

		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$this->set_pagination_args( [
			'total_items' => $this->total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->total_items / $per_page ),
		] );
	}

	/**
	 * get current status view
	 */
	private function get_current_status() {
		return ! empty( $_REQUEST['icon_status'] ) ? sanitize_text_field( $_REQUEST['icon_status'] ) : '';
	}

	protected function get_count_items_by_status( $status ) {
		$data = $this->get_items( 1, 99, '', $status );

		return isset( $data['total_items'] ) && $data['total_items'] ? absint( $data['total_items'] ) : 0;
	}

	/**
	 * get view filter by status
	 */
	protected function get_views() {
		$status = $this->get_current_status();

		$count = $this->get_count_items_by_status( $status );

		return [
			sprintf( '<a href="%s"%s>%s</a><span class="count">(%s)</span>', admin_url( 'admin.php?page=gutengeek-custom-icons' ), ! $status ? 'class="current"' : '', __( 'All', 'gutengeek' )
				, $this->get_count_items_by_status( 'publish' ) ),
			sprintf( '<a href="%s"%s>%s</a><span class="count">(%s)</span>', admin_url( 'admin.php?page=gutengeek-custom-icons&icon_status=publish' ), 'publish' === $status ? 'class="current"' :
				'',
				__( 'Published', 'gutengeek' ), $this->get_count_items_by_status( 'publish' ) ),
			sprintf( '<a href="%s"%s>%s</a><span class="count">(%s)</span>', admin_url( 'admin.php?page=gutengeek-custom-icons&icon_status=trash' ), 'trash' === $status ? 'class="current"' : '',
				__( 'Trash', 'gutengeek' ), $this->get_count_items_by_status( 'trash' ) ),
		];
	}
}
