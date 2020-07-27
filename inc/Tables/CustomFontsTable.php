<?php
namespace Gtg_Advanced_Blocks\Tables;

defined( 'ABSPATH' ) || exit();

class CustomFontsTable extends \WP_List_Table {

	public function __construct( $args = [] ) {
		parent::__construct($args);
	}

	public function get_sortable_columns() {
		$sortable_columns = [
			'title'  => array('font_name',false)
		];
		return $sortable_columns;
	}

	/**
	 * bulk actions
	 */
	public function get_bulk_actions() {
		$status = $this->get_current_status();
		$actions = [];
		if ( $status === 'publish' || ! $status ) {
		  	$actions = [
		  		'trash' => __('Trash', 'gutengeek')
		  	];
		} else if ( $status === 'trash' ) {
			$actions = [
				'restore' => __('Restore', 'gutengeek'),
		  		'delete' => __('Delete', 'gutengeek')
		  	];
		}
	  	return $actions;
	}

	public function column_cb($item) {
        return sprintf( '<input type="checkbox" name="fonts[]" value="%1$s" />', absint($item['font_id']) );
    }

    /**
     * print column name
     */
    public function column_title($item) {
    	$actions = [
    		'edit' => '<a href="'.admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $item['font_id']).'&action=edit">'.__('Edit', 'gutengeek').'</a>',
            'trash' =>'<a href="'.admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $item['font_id']).'&action=trash&nonce='.wp_create_nonce('gutengeek-nonce-trash-font').'">'.__('Trash', 'gutengeek').'</a>'
    	];

        $font_status = ! empty( $item['font_status']) ? sanitize_text_field( $item['font_status'] ) : '';
        if ( $font_status === 'trash' ) {
        	$actions = [
        		'restore' => '<a href="'.admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $item['font_id']).'&action=restore&nonce='.wp_create_nonce('gutengeek-nonce-restore-font').'">'.__('Restore', 'gutengeek').'</a>',
        		'delete' => '<a href="'.admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $item['font_id']).'&action=delete&nonce='.wp_create_nonce('gutengeek-nonce-delete-font').'" class="submitdelete">'.__('Delete', 'gutengeek').'</a>'
        	];
        }

        $state = $item['font_status'] === 'trash' ? ' -- <span class="post-state" style="font-size: 11px; color: #444; font-weight: bold">'.__('trashed', 'wpaopblocks').'</span>': '';
        return sprintf(
            '%1$s %3$s',
            $font_status === 'publish' ? sprintf( '<a href="%s"><b>%s</b>%s</a>', admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $item['font_id']), $item['font_name'], $state ) : sprintf( '<strong>%s</strong>', $item['font_name']),
            $item['font_id'],
            $this->row_actions($actions)
        );
    }

    /**
     * example
     */
    public function column_example($item) {
    	$item_data = gtg_get_font_data($item['font_id']);
    	$json_data = [ 'font_name' => ! empty($item_data['font_name']) ? $item_data['font_name'] : '' ];
    	if ( ! empty($item_data['items']) ) {
    		$json_data['font'] = $item_data['items'][0];
    	}
    	return sprintf( '<span class="example" data-font="%s">%s - %s</span>', _wp_specialchars(wp_json_encode($json_data), ENT_QUOTES, 'UTF-8', true), get_option( 'blogname' ), get_option( 'blogdescription' ));
    }

    /**
     * process trash bulk action
     *
     * @param $ids
     */
    public function process_trash_action( $ids = [] ) {
    	global $wpdb;
    	$count = 0;
		foreach ( $ids as $font_id ) {
			try {
				$wpdb->update(
					$wpdb->prefix . 'gtg_block_fonts',
					[ 'font_status' => 'trash' ],
					[ 'font_id' => $font_id ]
				);
				$count++;
			} catch ( \Exception $e ) {
				//
			}
		}

		return sprintf( __('%s fonts trashed', 'gutengeek'), $count );
    }

    /**
     * process restore bulk action
     *
     * @param $ids
     */
    public function process_retore_action( $ids = [] ) {
    	global $wpdb;
    	$count = 0;
		foreach ( $ids as $font_id ) {
			try {
				$wpdb->update(
					$wpdb->prefix . 'gtg_block_fonts',
					[ 'font_status' => 'publish' ],
					[ 'font_id' => $font_id ]
				);
				$count++;
			} catch ( \Exception $e ) {
				//
			}
		}

		return sprintf( __('%s fonts was re-published', 'gutengeek'), $count );
    }

    /**
     * process delete bulk action
     *
     * @param $ids
     */
    public function process_delete_action( $ids = [] ) {
    	global $wpdb;
    	$count = 0;
		foreach ( $ids as $font_id ) {
			try {
				$wpdb->delete(
					$wpdb->prefix . 'gtg_block_fonts',
					[ 'font_id' => $font_id ],
					[ '%d' ]
				);
				$count++;
			} catch ( \Exception $e ) {
				//
			}
		}

		return sprintf( __('%s fonts was deleted', 'gutengeek'), $count );
    }

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No fonts found.', 'gutengeek' );
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'	=> '<input type="checkbox" />',
			// 'font_id'	=> __( 'Description', 'gutengeek' ),
			'title' => __( 'Font Name', 'gutengeek' ),
			'example' => __( 'Font Example', 'gutengeek' ),
		);
	}

	/**
	 * get items
	 */
	public function get_items($current_page = 1, $per_page = 10, $search = '', $status = '', $orderBy = 'font_id', $order = 'DESC') {
		global $wpdb;
		$offset = ( $current_page - 1 ) * $per_page;

		$where = '';
		if ( $search ) {
			$where .= $wpdb->prepare(" AND name LIKE '%%s%'", $search);
		}
		if ( $status ) {
			$where .= $wpdb->prepare( " AND font_status = %s", $status );
		}
		$order = $wpdb->prepare( "ORDER BY {$orderBy} {$order} LIMIT %d OFFSET %d;", $per_page, $offset );

		$query = "SELECT SQL_CALC_FOUND_ROWS font_id, font_name, font_user_id, font_status FROM {$wpdb->prefix}gtg_block_fonts WHERE 1 = 1 {$where} {$order}";

		$items = $wpdb->get_results( $query, ARRAY_A );

		return [
			'items' => $items,
			'total_items' => $items ? $wpdb->get_var('SELECT FOUND_ROWS()') : 0
		];
	}

	/**
	 * hidden columns
	 */
	public function get_hidden_columns() {
		return [
			'font_id'
		];
	}

    public function prepare_items() {
    	$this->process_bulk_action();
    	$columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

		$per_page = $this->get_items_per_page( 'fonts_per_page', 10 );
		$current_page = $this->get_pagenum();
		$search = ! empty( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
		$status = ! empty( $_REQUEST['font_status']) ? sanitize_text_field( $_REQUEST['font_status'] ) : '';
		// only ncessary because we have sample data
		$orderBy = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field($_REQUEST['orderby']) : 'font_id';
		$order = ! empty( $_REQUEST['order'] ) ? sanitize_text_field($_REQUEST['order']) : 'DESC';
		$data = $this->get_items( $current_page, $per_page, $search, $status, $orderBy, $order );
		$this->items = $data['items'];
		$this->total_items = $data['total_items'];

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->set_pagination_args([
			'total_items' => $this->total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->total_items / $per_page ),
		]);
	}

	/**
	 * get current status view
	 */
	private function get_current_status() {
		return ! empty( $_REQUEST['font_status'] ) ? sanitize_text_field($_REQUEST['font_status']) : '';
	}

	/**
	 * get total font count
	 *
	 * @return int
	 */
	private function get_total_count() {
		global $wpdb;
		return $wpdb->get_var( "SELECT COUNT(font_id) FROM {$wpdb->prefix}gtg_block_fonts" );
	}

	/**
	 * get total font count
	 *
	 * @return int
	 */
	private function get_total_published_count() {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(font_id) FROM {$wpdb->prefix}gtg_block_fonts WHERE font_status = %s", 'publish') );
	}

	/**
	 * get total font count
	 *
	 * @return int
	 */
	private function get_total_trashed_count() {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(font_id) FROM {$wpdb->prefix}gtg_block_fonts WHERE font_status = %s", 'trash') );
	}

	/**
	 * get view filter by status
	 */
	protected function get_views() {
		$status = $this->get_current_status();
		return [
			sprintf( '<a href="%s"%s>%s<span class="count">(%d)</span></a>', admin_url('admin.php?page=gutengeek-custom-fonts' ), ! $status ? 'class="current"' : '', __('All', 'gutengeek'), $this->get_total_count() ),
			sprintf( '<a href="%s"%s>%s<span class="count">(%d)</span></a></a>', admin_url('admin.php?page=gutengeek-custom-fonts&font_status=publish' ), 'publish' === $status ? 'class="current"' : '', __('Published', 'gutengeek'), $this->get_total_published_count() ),
			sprintf( '<a href="%s"%s>%s<span class="count">(%d)</span></a></a>', admin_url('admin.php?page=gutengeek-custom-fonts&font_status=trash' ), 'trash' === $status ? 'class="current"' : '',__('Trash', 'gutengeek'), $this->get_total_trashed_count() ),
		];
	}
}
