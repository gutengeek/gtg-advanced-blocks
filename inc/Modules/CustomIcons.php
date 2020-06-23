<?php
namespace Gtg_Advanced_Blocks\Modules;

defined( 'ABSPATH' ) || exit();

use Gtg_Advanced_Blocks\Tables\CustomIconsTable as Table;

class CustomIcons {
	public $current_post_id = 0;

	const OPTION_NAME = 'gutengeek_custom_icons';

	public function __construct() {
		// $this->table = new Table();
	}

	/**
	 * register all hooks
	 *
	 * @var mixed
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_gutengeek_upload_custom_icon', [ $this, 'upload_custom_icon' ] );
		add_action( 'wp_ajax_gutengeek_update_custom_icon', [ $this, 'update_custom_icon' ] );
		add_action( 'gutengeek_custom_icons_after_update', [ $this, 'update_single_icon_set_option' ] );
		add_action( 'admin_init', [ $this, 'bulk_actions' ] );
		add_action( 'admin_init', [ $this, 'single_item_action' ] );
		add_action( 'gutengeek_single_icon_action_trash', [ $this, 'trash_item_action' ], 10, 3 );
		add_action( 'gutengeek_single_icon_action_restore', [ $this, 'restore_item_action' ], 10, 3 );
		add_action( 'gutengeek_single_icon_action_delete', [ $this, 'delete_item_action' ], 10, 3 );
		add_action( 'admin_notices', [ $this, 'print_custom_icon_notices' ] );
	}

	/**
	 * storage flash message to site transisent
	 */
	public function add_admin_notice( $message = '', $notice = 'error' ) {
		$messages = get_transient( 'gutengeek_custom_icon_notices' );
		if ( ! isset( $messages[ $notice ] ) ) {
			$messages[ $notice ] = [];
		}
		$messages[ $notice ][] = $message;
		set_transient( 'gutengeek_custom_icon_notices', $messages );
	}

	/**
	 * register menu callback
	 */
	public function register_menu() {
		add_submenu_page(
			GTG_AB_SLUG,
			__( 'Custom Icons', 'gutengeek' ),
			__( 'Custom Icons', 'gutengeek' ),
			'manage_options',
			GTG_AB_SLUG . '-custom-icons',
			[ $this, 'render_custom_icons' ],
			1
		);
	}

	public function get_options() {
		return get_option( self::OPTION_NAME, [] );
	}

	public function update_options( $options ) {
		update_option( self::OPTION_NAME, $options );
	}

	/**
	 * render custom icons page
	 */
	public function render_custom_icons() {
		$iconId      = ! empty( $_REQUEST['icon'] ) ? absint( $_REQUEST['icon'] ) : false;
		$isCreate    = isset( $_REQUEST['add_new'] );
		$this->table = new Table();
		if ( ! $iconId && $isCreate === false ) : ?>
			<div class="wrap" id="gutengeek-blocks-custom-icons">
				<h1 class="wp-heading-inline">
					<?php _e( 'Custom Icons', 'gutengeek' ) ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=gutengeek-custom-icons&add_new' ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'gutengeek' ) ?></a>
				</h1>
				<form action="" id="custom-icons-form" method="POST" enctype="multipart/form-data">
					<?php
					$this->table->views();
					$this->table->prepare_items();
					$this->table->display();
					wp_nonce_field( 'gutengeek-custom-icon-list-view' );
					?>
				</form>
			</div>
		<?php else : ?>
			<div class="wrap" id="gutengeek-blocks-custom-icons-form"></div>
		<?php endif;
	}

	/**
	 * enqueue scripts
	 */
	public function enqueue_scripts() {
		global $pagenow;
		$page     = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		$iconId   = isset( $_REQUEST['icon'] ) ? absint( $_REQUEST['icon'] ) : '';
		$isCreate = isset( $_REQUEST['add_new'] );
		if ( $pagenow !== 'admin.php' || $page !== 'gutengeek-custom-icons' || ( ! $iconId && $isCreate === false ) ) {
			return;
		}
		// enqueue scripts for custom icons page
		wp_enqueue_media();
		wp_enqueue_script( 'gutengeek-custom-icons', GTG_AB_URL . 'assets/js/admin/custom-icons.js', [ 'jquery', 'wp-util', 'svg-painter', 'wp-element', 'wp-i18n', 'gutengeek-admin-script' ],
			GTG_AB_VER );
		wp_localize_script( 'gutengeek-custom-icons', 'gutengeek_custom_icon_data', [
			'nonce'        => wp_create_nonce( 'gutengeek-save-icon-nonce' ),
			'icon'         => gtg_get_icon_data( $iconId ),
			'isEdit'       => $iconId ? 1 : 0,
			'addnew_url'   => esc_url( admin_url( 'admin.php?page=gutengeek-custom-icons&add_new' ) ),
			'custom_icons' => $this->get_options(),
		] );
	}

	/**
	 * Upload custom icon via AJAX.
	 */
	public function upload_custom_icon() {
		$nonce = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'gutengeek-save-icon-nonce' ) ) {
			wp_send_json_error( 403, [
				'message' => __( 'Permission denied.', 'gutengeek' ),
			] );
		}

		try {
			$upload_handler = $this->custom_icons_upload_handler();

			if ( is_wp_error( $upload_handler ) ) {
				throw new \Exception( $upload_handler->get_error_message() );
			}

			$icon_name = ! empty( $_REQUEST['icon_name'] ) ? sanitize_text_field( $_REQUEST['icon_name'] ) : $upload_handler['name'];
			$request = $_REQUEST;
			$request = array_merge( [
				'icon_name'     => $icon_name,
				'icon_dir_name' => $upload_handler['dir_name'],
			], $request );

			$icon_id = $this->process_save( $request );

			if ( is_wp_error( $icon_id ) ) {
				throw new \Exception( $icon_id->get_error_message() );
			}

			if ( isset( $_REQUEST['icon_id'] ) && $_REQUEST['icon_id'] ) {
				$redirect = false;
			} else {
				$redirect = true;
			}

			// Re-set id & name
			$config                  = $upload_handler['config'];
			$config['id']            = $icon_id;
			$config['icon_set_name'] = $icon_name;

			// Update options.
			$options                                = $this->get_options();
			$options[ $upload_handler['dir_name'] ] = $config;
			$this->update_options( $options );

			$response = [
				'icon'    => gtg_get_icon_data( $icon_id ),
				'message' => sprintf( __( '%s was updated successfull', 'gutengeek' ), $icon_name ),
			];

			if ( $redirect ) {
				$response['redirect'] = admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $icon_id . '&action=edit' );
			}

			wp_send_json_success( $response, 200 );
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			], 400 );
		}
	}

	/**
	 * Save custom icon via AJAX.
	 */
	public function update_custom_icon() {
		$nonce = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'gutengeek-save-icon-nonce' ) ) {
			wp_send_json_error( 403, [
				'message' => __( 'Permission denied.', 'gutengeek' ),
			] );
		}

		try {
			$icon_name = ! empty( $_REQUEST['icon_name'] ) ? sanitize_text_field( $_REQUEST['icon_name'] ) : '';

			$request = $_REQUEST;
			$request = array_merge( [
				'icon_name' => $icon_name,
			], $request );

			$icon_id = $this->process_save( $request );

			if ( is_wp_error( $icon_id ) ) {
				throw new \Exception( $icon_id->get_error_message() );
			}

			if ( isset( $_REQUEST['icon_id'] ) && $_REQUEST['icon_id'] ) {
				$redirect = true;
			} else {
				$redirect = true;
			}

			$response = [
				'icon'    => gtg_get_icon_data( $icon_id ),
				'message' => sprintf( __( '%s was updated successfull', 'gutengeek' ), $icon_name ),
			];

			if ( $redirect ) {
				$response['redirect'] = admin_url( 'admin.php?page=gutengeek-custom-icons&icon=' . $icon_id . '&action=edit' );
			}

			wp_send_json_success( $response, 200 );
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			], 400 );
		}
	}

	/**
	 * Save icon set.
	 *
	 * @return \WP_Error | object | int
	 */
	public function process_save( $request = [] ) {
		global $wpdb;
		try {
			$wpdb->query( 'START TRANSACTION' );
			$nonce = ! empty( $request['nonce'] ) ? $request['nonce'] : '';
			if ( ! wp_verify_nonce( $nonce, 'gutengeek-save-icon-nonce' ) ) {
				return new \WP_Error( 'gtg_none_invalid', __( 'Invalid nonce', 'gutengeek' ) );
			}

			$icon_id = isset( $request['icon_id'] ) ? absint( $request['icon_id'] ) : false;

			$icon_id = $this->process_save_icon( apply_filters( 'gutengeek_process_save_icon', $request, $icon_id ) );
			if ( is_wp_error( $icon_id ) ) {
				return $icon_id;
			}

			$wpdb->query( 'COMMIT' );

			return $icon_id;
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new \WP_Error( 'process_save_icon_error', $e->getMessage() );
		}
	}

	/**
	 * Save icon data
	 */
	public function process_save_icon( $request ) {
		global $wpdb;
		// check icon name valid
		$icon_id = isset( $request['icon_id'] ) ? absint( $request['icon_id'] ) : false;

		if ( ! $icon_id ) {
			if ( ! isset( $request['icon_dir_name'] ) || ! $request['icon_dir_name'] ) {
				return new \WP_Error( 'missing_zip_file', __( 'Missing .zip file or can not upload.', 'gutengeek' ) );
			}

			$inserted = $wpdb->insert( $wpdb->prefix . 'gutengeek_block_icons', [
				'icon_name'     => $request['icon_name'],
				'icon_dir_name' => $request['icon_dir_name'],
				'icon_user_id'  => get_current_user_id(),
				'icon_status'   => 'publish',
			], [ '%s', '%s', '%d', '%s' ] );
			$icon_id  = $inserted ? $wpdb->insert_id : false;

			do_action( 'gutengeek_custom_icons_after_insert', $icon_id, $request );
		} else {
			$updated = $wpdb->update(
				$wpdb->prefix . 'gutengeek_block_icons',
				[
					'icon_name'    => $request['icon_name'],
					'icon_user_id' => get_current_user_id(),
				],
				[ 'icon_id' => absint( $icon_id ) ],
				[ '%s', '%d' ]
			);

			do_action( 'gutengeek_custom_icons_after_update', $icon_id, $request );
		}
		do_action( 'gutengeek_custom_icons_saved', $icon_id );
		if ( $icon_id ) {
			return $icon_id;
		} else {
			return new \WP_Error( 'process_save_icon_error', __( 'Can not update icon', 'gutengeek' ) );
		}
	}

	public function update_single_icon_set_option( $icon_id ) {
		$icon = gtg_get_icon_data( $icon_id );

		$icon_name     = $icon['icon_name'];
		$icon_dir_name = $icon['icon_dir_name'];

		$options = $this->get_options();

		// Update options.
		if ( isset( $options[ $icon_dir_name ] ) ) {
			$options[ $icon_dir_name ]['icon_set_name'] = $icon_name;
			$this->update_options( $options );
		}
	}

	/**
	 * check icon name valid
	 *
	 * @param $icon_id  int
	 * @param $icon_nam string
	 */
	private function is_valid_icon_name( $icon_id, $icon_name = '' ) {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT icon_id FROM {$wpdb->prefix}gutengeek_block_icons WHERE 1 = 1 AND icon_name = %s", $icon_name );
		if ( $icon_id ) {
			$query .= $wpdb->prepare( " AND icon_id != %d", $icon_id );
		}

		return apply_filters( 'gtg_is_icon_name_valid', ! $wpdb->get_var( $query ), $icon_id, $icon_name );
	}

	public function bulk_actions() {
		if ( ! isset( $_GET['page'] ) || 'gutengeek-custom-icons' !== sanitize_text_field( $_GET['page'] ) ) {
			return;
		}

		$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		$nonce  = ! empty( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
		if ( ! $action || ! $nonce ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, 'gutengeek-custom-icon-list-view' ) ) {
			wp_die( __( 'Permission denied.' ) );
		}

		global $wpdb;
		$icon_ids = ! empty( $_REQUEST['icons'] ) ? array_map( 'absint', $_REQUEST['icons'] ) : [];
		switch ( $action ) {
			case 'trash':
				$count = 0;
				foreach ( $icon_ids as $icon_id ) {
					try {
						$wpdb->update(
							$wpdb->prefix . 'gutengeek_block_icons',
							[ 'icon_status' => 'trash' ],
							[ 'icon_id' => $icon_id ]
						);
					} catch ( \Exception $e ) {
						$count++;
					}
				}
				$this->add_admin_notice( sprintf( __( '%d icons trashed', 'gutengeek' ), count( $icon_ids ) - $count ), 'success' );

				break;
			case 'restore':
				$count = 0;
				foreach ( $icon_ids as $icon_id ) {
					try {
						$wpdb->update(
							$wpdb->prefix . 'gutengeek_block_icons',
							[ 'icon_status' => 'publish' ],
							[ 'icon_id' => $icon_id ]
						);
					} catch ( \Exception $e ) {
						$count++;
					}
				}
				$this->add_admin_notice( sprintf( __( '%d icons was re-published', 'gutengeek' ), count( $icon_ids ) - $count ), 'success' );
				break;
			case 'delete':
				$count = 0;
				foreach ( $icon_ids as $icon_id ) {
					try {
						$icon           = gtg_get_icon_data( $icon_id );
						$icon_dir_name  = $icon['icon_dir_name'];
						$icon_directory = self::get_upload_folder_directory( $icon_dir_name );
						$wpdb->delete(
							$wpdb->prefix . 'gutengeek_block_icons',
							[ 'icon_id' => $icon_id ],
							[ '%d' ]
						);

						// remove icon set assets directory
						if ( ! empty( $icon_directory ) && is_dir( $icon_directory ) ) {
							self::get_wp_filesystem()->rmdir( $icon_directory, true );
						}

						$options = $this->get_options();
						if ( isset( $options[ $icon_dir_name ] ) ) {
							unset( $options[ $icon_dir_name ] );
							$this->update_options( $options );
						}
					} catch ( \Exception $e ) {
						$count++;
					}
				}
				$this->add_admin_notice( sprintf( __( '%d icons was deleted', 'gutengeek' ), count( $icon_ids ) - $count ), 'success' );
				break;
			default:
				break;
		}

		wp_safe_redirect( 'admin.php?page=gutengeek-custom-icons' );
	}

	/**
	 * single item action
	 * restore | delete | trash
	 */
	public function single_item_action() {
		$nonce   = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		$icon_id = ! empty( $_REQUEST['icon'] ) ? absint( $_REQUEST['icon'] ) : '';
		$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		if ( ! $nonce || ! $action || ! $icon_id ) {
			return;
		}

		// get icon data
		$icon = gtg_get_icon_data( $icon_id );
		if ( ! wp_verify_nonce( $nonce, 'gutengeek-nonce-' . $action . '-icon' ) || ! $icon || is_wp_error( $icon ) ) {
			wp_die( __( 'Permission denied.' ) );
		}

		// trigger action
		do_action( 'gutengeek_single_icon_action_' . $action, $action, $icon_id, $icon );
	}

	/**
	 * trash icon
	 */
	public function trash_item_action( $action, $icon_id, $icon ) {
		global $wpdb;
		// update status
		$wpdb->update( $wpdb->prefix . 'gutengeek_block_icons', [ 'icon_status' => 'trash' ], [ 'icon_id' => $icon_id ] );

		$icon_name = $icon['icon_name'];

		// add notice
		$this->add_admin_notice( sprintf( __( '%s was move to trash', 'gutengeek' ), $icon_name ), 'success' );

		wp_safe_redirect( admin_url( 'admin.php?page=gutengeek-custom-icons' ) );
	}

	/**
	 * restore single item
	 */
	public function restore_item_action( $action, $icon_id, $icon ) {
		global $wpdb;
		// update status
		$wpdb->update( $wpdb->prefix . 'gutengeek_block_icons', [ 'icon_status' => 'publish' ], [ 'icon_id' => $icon_id ] );

		$icon_name = $icon['icon_name'];
		// add notice
		$this->add_admin_notice( sprintf( __( '%s was restored', 'gutengeek' ), $icon_name ), 'success' );

		wp_safe_redirect( admin_url( 'admin.php?page=gutengeek-custom-icons' ) );
	}

	/**
	 * delete item action
	 */
	public function delete_item_action( $action, $icon_id, $icon ) {
		global $wpdb;
		// update status
		$wpdb->delete( $wpdb->prefix . 'gutengeek_block_icons', [ 'icon_id' => $icon_id ] );

		$icon_name      = $icon['icon_name'];
		$icon_dir_name  = $icon['icon_dir_name'];
		$icon_directory = self::get_upload_folder_directory( $icon_dir_name );

		// remove icon set assets directory
		if ( ! empty( $icon_directory ) && is_dir( $icon_directory ) ) {
			self::get_wp_filesystem()->rmdir( $icon_directory, true );
		}

		$options = $this->get_options();
		if ( isset( $options[ $icon_dir_name ] ) ) {
			unset( $options[ $icon_dir_name ] );
			$this->update_options( $options );
		}

		// add notice
		$this->add_admin_notice( sprintf( __( '%s was deleted', 'gutengeek' ), $icon_name ), 'success' );

		wp_safe_redirect( admin_url( 'admin.php?page=gutengeek-custom-icons' ) );
	}

	/**
	 * print custom icon admin notices
	 */
	public function print_custom_icon_notices() {
		$notices = get_transient( 'gutengeek_custom_icon_notices', [] );
		if ( ! $notices ) {
			return;
		}
		$types   = array_keys( $notices );

		foreach ( $types as $type ): ?>
			<?php if ( ! empty( $notices[ $type ] ) ) : ?>

				<?php foreach ( $notices[ $type ] as $message ) : ?>

					<div class="notice <?php echo esc_attr( $type ) ?>"><p><?php printf( '%s', $message ) ?></p></div>

				<?php endforeach; ?>

			<?php endif; ?>

		<?php endforeach;

		// clear
		set_transient( 'gutengeek_custom_icon_notices', [] );
	}

	public static function get_supported_icon_sets() {
		$icon_sets = [
			'icomoon'   => 'Gtg_Advanced_Blocks\Modules\Icon_Sets\Icomoon',
			'fontastic' => 'Gtg_Advanced_Blocks\Modules\Icon_Sets\Fontastic',
		];

		return array_merge( apply_filters( 'gutengeek_custom_icon_supported_types', [] ), $icon_sets );
	}

	/**
	 * get_wp_filesystem
	 *
	 * @return \WP_Filesystem_Base
	 */
	public static function get_wp_filesystem() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	private function upload() {
		$file     = ! empty($_FILES['file']) ? $_FILES['file'] : [];
		$filename = isset($file['name']) ? sanitize_file_name($file['name']) : '';
		$ext      = pathinfo( $filename, PATHINFO_EXTENSION );
		if ( 'zip' !== $ext ) {
			unlink( $_FILES['file']['name'] );

			return new \WP_Error( 'unsupported_file', __( 'Only zip files are allowed', 'gutengeek' ) );
		}
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		// Handler upload archive file.
		$upload_result = wp_handle_upload( $file, [ 'test_form' => false ] );
		if ( isset( $upload_result['error'] ) ) {
			unlink( $_FILES['file']['name'] );

			return new \WP_Error( 'upload_error', $upload_result['error'] );
		}

		return $upload_result['file'];
	}

	private function extract_zip( $file, $to ) {
		$unzip_result = unzip_file( $file, $to );
		@unlink( $file );

		return $unzip_result; // TRUE | WP_Error instance.
	}

	private function upload_and_extract_zip() {
		$zip_file = $this->upload();
		if ( is_wp_error( $zip_file ) ) {
			return $zip_file;
		}
		$filesystem = self::get_wp_filesystem();
		$extract_to = trailingslashit( get_temp_dir() . pathinfo( $zip_file, PATHINFO_FILENAME ) );

		$unzipped = $this->extract_zip( $zip_file, $extract_to );
		if ( is_wp_error( $unzipped ) ) {
			return $unzipped;
		}

		// Find the right folder.
		$source_files = array_keys( $filesystem->dirlist( $extract_to ) );
		if ( count( $source_files ) === 0 ) {
			return new \WP_Error( 'incompatible_archive', esc_html__( 'Incompatible archive', 'gutengeek' ) );
		}

		if ( 1 === count( $source_files ) && $filesystem->is_dir( $extract_to . $source_files[0] ) ) {
			$directory = $extract_to . trailingslashit( $source_files[0] );
		} else {
			$directory = $extract_to;
		}

		return [
			'directory'    => $directory,
			'extracted_to' => $extract_to,
		];
	}

	public function custom_icons_upload_handler() {
		$results = $this->upload_and_extract_zip();
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		$supported_icon_sets = self::get_supported_icon_sets();
		foreach ( $supported_icon_sets as $key => $handler ) {
			/**
			 * @var Icon_Sets\Icon_Set_Base $icon_set_handler
			 */
			$icon_set_handler = new $handler( $results['directory'] );

			if ( ! $icon_set_handler ) {
				continue;
			}

			if ( ! $icon_set_handler->is_valid() ) {
				continue;
			}

			$icon_set_handler->move_files();
			$config = $icon_set_handler->build_config();

			return [
				'name'     => $icon_set_handler->get_name(),
				'dir_name' => $icon_set_handler->get_dir_name(),
				'config'   => $config,
			];
		}

		return new \WP_Error( 'unsupported_zip_format', __( 'The zip file provided is not supported!', 'gutengeek' ) );
	}

	public static function get_upload_folder_path() {
		$wp_upload_dir = wp_upload_dir();
		$path          = $wp_upload_dir['basedir'] . '/gutengeek/custom-icons';

		/**
		 * Upload file path.
		 *
		 * Filters the path to a file uploaded using Gtg_Advanced_Blocks\ forms.
		 *
		 * @param string $path      File path.
		 * @param string $file_name File name.
		 *
		 */
		$path = apply_filters( 'gutengeek_custom_icons_folder_path', $path );

		return $path;
	}

	public static function get_upload_folder_directory( $dir_name ) {
		$path = self::get_upload_folder_path() . '/' . $dir_name;

		return $path;
	}
}
