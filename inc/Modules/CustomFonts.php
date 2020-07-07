<?php
namespace Gtg_Advanced_Blocks\Modules;
use Gtg_Advanced_Blocks\Tables\CustomFontsTable as Table;

defined( 'ABSPATH' ) || exit();

class CustomFonts {

	/**
	 * register all hooks
	 *
	 * @var mixed
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_wp_ajax_gutengeek_save_font', [ $this, 'save_custom_font' ] );
		add_filter( 'set-screen-option', [ $this, 'set_screen_options' ], 10, 3 );
		add_action( 'admin_init', [ $this, 'single_item_action' ] );
		add_action( 'gutengeek_single_font_action_trash', [ $this, 'trash_item_action' ], 10, 3 );
		add_action( 'gutengeek_single_font_action_restore', [ $this, 'restore_item_action' ], 10, 3 );
		add_action( 'gutengeek_single_font_action_delete', [ $this, 'delete_item_action' ], 10, 3 );
		add_action( 'admin_notices', [ $this, 'print_custom_font_notices']);
	}

	/**
	 * storage flash message to site transisent
	 */
	public function add_admin_notice( $message = '', $notice = 'error' ) {
		$messages = get_transient( 'gutengeek_custom_font_notices', [] );
		if ( ! isset($messages[$notice]) ) {
			$messages[$notice] = [];
		}
		$messages[$notice][] = $message;
		set_transient( 'gutengeek_custom_font_notices', $messages );
	}

	/**
	 * register menu callback
	 */
	public function register_menu() {
		$hook = add_submenu_page(
			GTG_AB_SLUG,
			__( 'Custom Fonts', 'gutengeek' ),
			__( 'Custom Fonts', 'gutengeek' ),
			'manage_options',
			GTG_AB_SLUG . '-custom-fonts',
			[ $this, 'render_custom_fonts' ],
			1
		);
		add_action( "load-{$hook}", [ $this, 'init_page_screen' ] );
	}

	public function set_screen_options( $status, $option, $value ) {
		return $value;
	}

	/**
	 * init table on page init
	 */
	public function init_page_screen() {
		$option = 'per_page';
		$args   = [
			'label'   => __( 'Fonts per page', 'gutengeek' ),
			'default' => 10,
			'option'  => 'fonts_per_page'
		];

		add_screen_option( $option, $args );
		$this->table = new Table();
		$this->process_bulk_actions();
	}

	/**
	 * render custom fonts page
	 */
	public function render_custom_fonts() {
		$fontId = ! empty( $_REQUEST['font'] ) ? absint( $_REQUEST['font'] ) : false;
		$isCreate = isset( $_REQUEST['add_new'] );
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page']) : '';
		$this->table->prepare_items();
		if ( ! $fontId && $isCreate === false ) : ?>
			<div class="wrap" id="gutengeek-blocks-custom-fonts">
				<h1 class="wp-heading-inline">
					<?php _e( 'Custom Fonts', 'gutengeek' ) ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=gutengeek-custom-fonts&add_new' )) ?>" class="page-title-action"><?php _e('Add New', 'gutengeek') ?></a>
				</h1>
				<form id="gutengeek-custom-fonts-form" method="get">
					<input type="hidden" name="page" value="<?php echo esc_attr( $page ) ?>" />
					<?php
						$this->table->views();
						$this->table->display();
						wp_nonce_field( 'gutengeek-fonts-table' );
					?>
				</form>
			</div>
		<?php else : ?>
			<div class="wrap" id="gutengeek-blocks-custom-fonts-form"></div>
		<?php endif;
	}

	/**
	 * enqueue scripts
	 */
	public function enqueue_scripts() {
		global $pagenow;
		$page = isset($_REQUEST['page']) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		$fontId = isset($_REQUEST['font']) ? absint($_REQUEST['font']) : '';
		$isCreate = isset( $_REQUEST['add_new'] );
		if ( $pagenow !== 'admin.php' || $page !== 'gutengeek-custom-fonts' ) {// || (! $fontId && $isCreate === false)
			return;
		}
		// enqueue scripts for custom fonts page
		wp_enqueue_media();
		wp_enqueue_script( 'gutengeek-custom-fonts', GTG_AB_URL . 'assets/js/admin/custom-fonts.js', [ 'jquery', 'wp-util', 'svg-painter', 'wp-element', 'wp-i18n', 'gutengeek-admin-script' ],
			GTG_AB_VER );
		wp_localize_script( 'gutengeek-custom-fonts', 'gutengeek_custom_font_data', [
			'save_nonce' => wp_create_nonce( 'gutengeek-save-font-nonce' ),
			'trash_url' => admin_url('admin.php?page=gutengeek-custom-fonts&font=' . $fontId).'&action=trash&nonce='.wp_create_nonce('gutengeek-nonce-trash-font'),
			'font' => gtg_get_font_data( $fontId ),
		]);
	}

	/**
	 * save font process
	 *
	 * @return font object | WP_Error
	 */
	public function process_save($request = []) {
		global $wpdb;
		try {
			$wpdb->query('START TRANSACTION');
			$nonce = ! empty($request['nonce']) ? $request['nonce'] : '';
			if ( ! wp_verify_nonce($nonce, 'gutengeek-save-font-nonce') ) {
				return new \WP_Error( 'gtg_none_invalid', __( 'Invalid nonce', 'gutengeek' ) );
			}
			$font_id = isset($request['font_id']) ? absint( $request['font_id'] ) : false;

			$font_data = ! empty($request['font']) ? $request['font'] : [];
			if ( is_wp_error($font_data) ) {
				return $font_data;
			}

			$font_id = $this->process_save_font( apply_filters( 'gutengeek_process_save_font', $font_data, $font_id, $request ) );
			if ( is_wp_error($font_id) ) {
				return $font_id;
			}

			if ( ! empty($font_data['items']) ) {
				foreach ( $font_data['items'] as $item ) {
					$font_item_id = $this->process_save_font_item($font_id, $item);
					if ( is_wp_error( $font_item_id ) ) {
						throw new \Exception( $font_item_id->get_error_message() );
					} else {
						// save font files
						$files = ! empty( $item['items'] ) ? $item['items'] : [];
						if ( $files ) {
							foreach( $files as $file ) {
								$attach_file_id = $this->process_save_font_file( $font_item_id, $file );
								if ( is_wp_error( $attach_file_id ) ) {
									throw new \Exception( $attach_file_id->get_error_message() );
								}
							}
						}
					}
				}
			}

			$wpdb->query('COMMIT');

			return $font_id;
		} catch ( \Exception $e ) {
			$wpdb->query('ROLLBACK');
			return new \WP_Error( 'process_save_font_error', $e->getMessage() );
		}
	}

	/**
	 * save font data
	 */
	public function process_save_font($font_data) {
		global $wpdb;
		// check font name valid
		$font_id = isset($font_data['font_id']) ? absint( $font_data['font_id'] ) : false;
		$font_name = isset($font_data['font_name']) ? sanitize_text_field( $font_data['font_name'] ) : '';
		$valid = $this->is_valid_font_name($font_id, $font_name);
		if ( ! $valid ) {
			return new \WP_Error( 'font_name_is_taken', sprintf( __( 'The font name is already taken', 'gutengeek'), $font_name ));
		}

		if ( ! $font_id ) {
			$inserted = $wpdb->insert( $wpdb->prefix . 'gtg_block_fonts', [
				'font_name' => $font_data['font_name'],
				'font_user_id' => get_current_user_id(),
				'font_status' => 'publish'
			], ['%s', '%d', '%s'] );
			$font_id = $inserted ? $wpdb->insert_id : false;
		} else {
			$updated = $wpdb->update(
				$wpdb->prefix . 'gtg_block_fonts',
				[
					'font_name' => $font_data['font_name'],
					'font_status' => $font_data['font_status'],
					'font_user_id' => get_current_user_id(),
				],
				[ 'font_id' => absint($font_id) ],
				[ '%s', '%s', '%d' ]
			);
		}
		do_action( 'gutengeek_custom_fonts_saved', $font_id );
		if ( $font_id ) {
			return $font_id;
		} else {
			return new \WP_Error( 'process_save_font_error', __('Can not update font', 'gutengeek') );
		}
	}

	/**
	 * check font name valid
	 *
	 * @param $font_id int
	 * @param $font_nam string
	 */
	private function is_valid_font_name( $font_id, $font_name = '' ) {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT font_id FROM {$wpdb->prefix}gtg_block_fonts WHERE 1 = 1 AND font_name = %s", $font_name );
		if ( $font_id ) {
			$query .= $wpdb->prepare( " AND font_id != %d", $font_id );
		}

		return apply_filters( 'gtg_is_font_name_valid', ! $wpdb->get_var( $query ), $font_id, $font_name );
	}

	/**
	 * save font item
	 */
	private function process_save_font_item( $font_id, $item = [] ) {
		// trigger before save font item
		do_action( 'gutengeek_before_save_font_item', $font_id, $item );
		global $wpdb;

		$font_item_id = ! empty( $item['font_item_id'] ) ? absint( $item['font_item_id'] ) : '';
		if ( ! $font_item_id ) {
			$inserted = $wpdb->insert(
				$wpdb->prefix . 'gtg_block_font_items',
				[
					'font_item_weight' => ! empty( $item['font_item_weight'] ) ? $item['font_item_weight'] : '',
					'font_item_style' => ! empty( $item['font_item_style'] ) ? $item['font_item_style'] : '',
					'font_id' => absint($font_id)
				],
				[ '%s', '%s', '%d' ]
			);
			$font_item_id = $wpdb->insert_id;
		} else {
			$wpdb->update(
				$wpdb->prefix . 'gtg_block_font_items',
				[
					'font_item_weight' => $item['font_item_weight'],
					'font_item_style' => $item['font_item_style'],
					'font_id' => absint($font_id)
				],
				[ 'font_item_id' => absint($font_item_id) ],
				[ '%s', '%s', '%d' ]
			);
		}

		return apply_filters( 'gutengeek_save_font_item_processed', $font_item_id, $font_id, $item );
	}

	/**
	 * process save font file item
	 *
	 * @param $font_item_id init
	 * @param $file array
	 */
	private function process_save_font_file( $font_item_id, $file ) {
		do_action( 'gutengeek_before_process_save_font_file', $font_item_id, $file );
		global $wpdb;
		$font_item_attach_id = ! empty( $file['font_item_attach_id'] ) ? absint( $file['font_item_attach_id'] ) : '';
		if ( ! $font_item_attach_id ) {
			$inserted = $wpdb->insert(
				$wpdb->prefix . 'gtg_block_font_item_attachments',
				[
					'font_item_attach_type' => ! empty( $file['font_item_attach_type'] ) ? $file['font_item_attach_type'] : '',
					'font_attach_id' =>  ! empty( $file['font_attach_id'] ) ? absint($file['font_attach_id']) : '',
					'font_item_id' => $font_item_id
				],
				[
					'%s',
					'%d',
					'%d'
				]
			);

			if ( ! $inserted ) {
				return new \WP_Error( 'save_font_file_error', __( 'Error processing font file', 'gutengeek' ) );
			}
		} else {
			$wpdb->update(
				$wpdb->prefix . 'gtg_block_font_item_attachments',
				[
					'font_item_attach_type' => ! empty( $file['font_item_attach_type'] ) ? $file['font_item_attach_type'] : '',
					'font_attach_id' =>  ! empty( $file['font_attach_id'] ) ? absint($file['font_attach_id']) : '',
					'font_item_id' => $font_item_id
				],
				[ 'font_item_attach_id' => $font_item_attach_id ],
				[
					'%s',
					'%s',
					'%d'
				]
			);
		}

		// return inserted id
		return apply_filters( 'gutengeek_save_font_item_processed', $wpdb->insert_id, $font_item_id, $file );
	}

	public function process_bulk_actions() {
		if ( ! isset( $_GET['page'] ) || 'gutengeek-custom-fonts' !== sanitize_text_field( $_GET['page'] ) ) {
			return;
		}

		$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field($_REQUEST['action']) : '';
		$page = ! empty( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		$nonce = ! empty( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
		if ( $page !== 'gutengeek-custom-fonts' || ! $action || ! $nonce ) {
			return;
		}
		if ( ! wp_verify_nonce( $nonce, 'gutengeek-fonts-table' ) ) {
			wp_die( __( 'Permission denied.', 'gutengeek' ) );
		}

		global $wpdb;
		$font_ids = ! empty( $_REQUEST['fonts'] ) ? array_map( 'absint', $_REQUEST['fonts'] ) : [];
		switch ( $action ) {
			case 'trash':
				$this->add_admin_notice( $this->table->process_trash_action( $font_ids ), 'updated' );
			break;

			case 'restore':
				$this->add_admin_notice( $this->table->process_retore_action( $font_ids ), 'updated' );
			break;

			case 'delete':
				$this->add_admin_notice( $this->table->process_delete_action( $font_ids ), 'updated' );
			break;

			default:
				break;
		}

		wp_safe_redirect( 'admin.php?page=gutengeek-custom-fonts' ); exit();
	}

	/**
	 * single item action
	 * restore | delete | trash
	 */
	public function single_item_action() {
		$nonce = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		$font_id = ! empty( $_REQUEST['font'] ) ? absint( $_REQUEST['font'] ) : '';
		$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		$page = ! empty( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';

		if ( $page !== 'gutengeek-custom-fonts' || ! $nonce || ! $action || ! $font_id ) {
			return;
		}

		// get font data
		$font = gtg_get_font_data( $font_id );
		if ( ! wp_verify_nonce($nonce, 'gutengeek-nonce-'.$action.'-font') || ! $font || is_wp_error($font) )  {
			wp_die( __( 'Permission denied.', 'gutengeek' ) );
		}

		// trigger action
		do_action( 'gutengeek_single_font_action_' . $action, $action, $font_id, $font );
	}

	/**
	 * trash font
	 */
	public function trash_item_action( $action, $font_id, $font ) {
		global $wpdb;
		// update status
		$wpdb->update( $wpdb->prefix . 'gtg_block_fonts', [ 'font_status' => 'trash' ], [ 'font_id' => $font_id ] );

		$font_name = $font['font_name'];
		// add notice
		$this->add_admin_notice( sprintf( __( '%s was move to trash', 'gutengeek' ), $font_name ), 'updated' );

		wp_safe_redirect( admin_url('admin.php?page=gutengeek-custom-fonts') ); exit();
	}

	/**
	 * restore single item
	 */
	public function restore_item_action( $action, $font_id, $font ) {
		global $wpdb;
		// update status
		$wpdb->update( $wpdb->prefix . 'gtg_block_fonts', [ 'font_status' => 'publish' ], [ 'font_id' => $font_id ] );

		$font_name = $font['font_name'];
		// add notice
		$this->add_admin_notice( sprintf( __( '%s was restored', 'gutengeek' ), $font_name ), 'updated' );

		wp_safe_redirect( admin_url('admin.php?page=gutengeek-custom-fonts') ); exit();
	}

	/**
	 * delete item action
	 */
	public function delete_item_action( $action, $font_id, $font ) {
		global $wpdb;
		// update status
		$wpdb->delete( $wpdb->prefix . 'gtg_block_fonts', [ 'font_id' => $font_id ] );

		$font_name = $font['font_name'];
		// add notice
		$this->add_admin_notice( sprintf( __( '%s was deleted', 'gutengeek' ), $font_name ), 'updated' );

		wp_safe_redirect( admin_url('admin.php?page=gutengeek-custom-fonts') ); exit();
	}

	/**
	 * save custom font ajax callback
	 */
	public function save_custom_font() {
		$nonce = ! empty($_REQUEST['nonce']) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		if ( ! $nonce || ! wp_verify_nonce($nonce, 'gutengeek-save-font-nonce') ) {
			wp_send_json_error( 403, [
				'message' => __('Permission denied')
			] );
		}

		try {
			$redirect = false;
			$font_id = $this->process_save( $_REQUEST );
			$post_font_id = isset( $_REQUEST['font'], $_REQUEST['font']['font_id'] ) ? absint( $_REQUEST['font']['font_id'] ) : [];
			$font_name = ! empty( $font['font_name'] ) ? $font['font_name'] : '';
			if ( is_wp_error($font_id) ) {
				throw new \Exception( $font_id->get_error_message() );
			}

			if ( isset($font['font_id']) && $font['font_id'] ) {
				$messasge = sprintf( '%s ' . __('was updated successfull', 'gutengeek'), $font_name );
			} else {
				$redirect = true;
				$messasge = sprintf( '%s ' . __('was created successfull', 'gutengeek'), $font_name );
			}
			$response = [
				'font' => gtg_get_font_data($font_id),
				'message' => sprintf( '%s ' . __('was updated successfull', 'gutengeek'), $font_name )
			];
			if ( $redirect ) {
				$response['redirect'] = admin_url( 'admin.php?page=gutengeek-custom-fonts&font=' . $font_id . '&action=edit' );
			}
			wp_send_json_success( $response, 200 );
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage()
			], 400 );
		}
	}

	/**
	 * print custom font admin notices
	 */
	public function print_custom_font_notices() {
		$notices = get_transient( 'gutengeek_custom_font_notices', [] );
		if ( ! $notices ) {
			return;
		}
		$types = array_keys( $notices );

		foreach ( $types as $type ): ?>
			<?php if ( ! empty( $notices[ $type ]) ) : ?>

			<?php foreach ( $notices[$type] as $message ) : ?>

				<div class="notice <?php echo esc_attr( $type )?>"><p><?php printf( '%s', $message ) ?></p></div>

			<?php endforeach; ?>

			<?php endif; ?>

		<?php endforeach;

		// clear
		set_transient( 'gutengeek_custom_font_notices', [] );
	}

}
