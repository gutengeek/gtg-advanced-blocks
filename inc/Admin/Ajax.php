<?php

namespace GutenGeek\Admin;

class Ajax {

	public static function init() {
		// fetch global settings
		add_action( 'wp_ajax_gutengeek-fetch-global-settings', [ __CLASS__, 'fetch_global_settings' ] );
		// save global settings each option name
		add_action( 'wp_ajax_gutengeek-live-save-settings', [ __CLASS__, 'live_save_settings' ] );

		// update global settings
		add_action( 'wp_ajax_gutengeek-save-settings', [ __CLASS__, 'update_settings' ] );

		// fetch templates from live site
		add_action( 'wp_ajax_gutengeek-fetch-templates', [ __CLASS__, 'fetch_templates' ] );
		// import template action
		add_action( 'wp_ajax_gutengeek-import-template', [ __CLASS__, 'import_template' ] );
		add_action( 'wp_ajax_gutengeek-remove-template', [ __CLASS__, 'remove_saved_local_template' ] );
		add_action( 'wp_ajax_gutengeek-fetch-google-fonts', [ __CLASS__, 'fetch_google_fonts' ] );
	}

	/**
	 * fetch global settings
	 */
	public static function fetch_global_settings() {
		if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) || ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [
				'message' => __( 'You don\'t have permission to access this page', 'gutengeek' ),
			]);
		}

		$options = get_option( '_gutengeek_global_settings', [] );
		$options = $options ? $options : [];

		wp_send_json_success( [ 'settings' => $options ] );
	}

	/**
	 * live save setting each option name
	 */
	public static function live_save_settings() {
		try {
			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) ) {
				throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			}

			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

			if ( ! $type ) {
				throw new \Exception( __( 'Key is not exists', 'gutengeek' ) );
			}

			$value = ! empty( $_POST[$type] ) ? $_POST[$type] : false;
			if ( $value === false ) {
				throw new \Exception( __( 'Value is invalid!', 'gutengeek' ) );
			}

			$options = get_option( '_gutengeek_global_settings', [] );
			$options[$type] = $value;
			update_option( '_gutengeek_global_settings', $options );

			wp_send_json_success( [
               'settings' => $value,
               'message' => __('Settings updated successfull', 'gutengeek')
			] );
		} catch (\Exception $e) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			]);
		}

	}

	/**
	 * update global setting page
	 */
	public function update_settings() {
		try {
			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) ) {
				throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			}

			$settings = ! empty($_POST['settings']) ? $_POST['settings'] : false;
			if ( ! $settings ) {
				throw new \Exception( __( 'Global settings cannot empty', 'gutengeek' ) );
			}
			update_option( '_gutengeek_global_settings', $settings );

			wp_send_json_success( [
               'settings' => get_option( '_gutengeek_global_settings' ),
               'message' => __('Settings updated successfull', 'gutengeek')
			] );
		} catch (\Exception $e) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			]);
		}
	}

	/**
	 * fetch templates from our live site
	 */
	public static function fetch_templates() {
		try {
			// throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) ) {
				throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			}
			$force = isset( $_REQUEST['force'] ) ? $_REQUEST['force'] : false;
			$items = get_transient( 'gutengeek_library_items', [] );

			if ( $force || ! $items ) {
				$args = [
					'method' => 'GET',
					'reject_unsafe_urls' => false,
					'body' => [
						'per_page' => isset( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 100,
						// 'template_type' => $type
					]
				];
				// validate type for request
				$type = ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : '';
				if ( $type ) {
					$args['body']['template_type'] = $type;
				}
				$args = apply_filters( 'gutengeek_fetch_remote_library_args', $args );

				// api request
				$items = self::fetch_remote_templates( $args );
				$items = array_merge( $items, gutengeek_get_saved_items() );

				// Save the results in a transient
				set_transient( 'gutengeek_library_items', $items, DAY_IN_SECONDS );
			}

			wp_send_json_success( [ 'items' => $items] );
		} catch (\Exception $e) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			]);
		}
	}

	/**
	 * try to get remote template
	 *
	 * @return mixed array or throw exception
	 */
	public static function fetch_remote_templates( $args = [] ) {

		if ( defined( 'WP_DEBUG' ) ) {
			add_filter( 'http_request_args', [ __CLASS__, 'remove_unsfafe_url' ], 10, 2 );
		}

		$request = wp_remote_get( gutengeek()->api->api_info_url, $args );
		if ( defined( 'WP_DEBUG' ) ) {
			remove_filter( 'http_request_args', [ __CLASS__, 'remove_unsfafe_url' ], 10, 2 );
		}
		$header_status = wp_remote_retrieve_response_code( $request );
		$response = wp_remote_retrieve_body( $request );

		if ( 200 === $header_status ) {
			if ( wp_json_encode($response) ) {
				$items = json_decode( $response, true );
				if ( ! $items ) {
					throw new \Exception( __( 'Sorry, we couldn\'t any templates.', 'gutengeek' ) );
				}

				// send response
				return apply_filters( 'gutengeek_fetch_remote_items_results', $items, $args );
			} else {
				throw new \Exception( __( 'Sorry, we couldn\'t find the match. Please try again later.', 'gutengeek' ) );
			}
		} else {
			$response = json_decode($response, true);
			if ( ! empty( $response['message'] ) ) {
				throw new \Exception( $response['message'] );
			} else {
				throw new \Exception( __( 'Opp! Your request got too far. Please try again later.', 'gutengeek' ) );
			}
		}
	}

	public static function remove_unsfafe_url($parsed_args) {
		$parsed_args['reject_unsafe_urls'] = false;
		return $parsed_args;
	}

	/**
	 * import template
	 */
	public static function import_template() {
		try {
			// throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) ) {
				throw new \Exception( __( 'You don\'t have permission to import', 'gutengeek' ) );
			}

			$id = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : false;
			$local_id = ! empty( $_REQUEST['local_id'] ) ? absint( $_REQUEST['local_id'] ) : false;

			if ( $local_id ) {
				// import local storage template
				$post = get_post( $local_id );
				$post_content = $post->post_content;
				wp_send_json_success( [
					'data' => $post_content
				] );
			} else {
				// import from remote server
				if ( ! $id ) {
					throw new Exception( __( 'Section/Page not found. Maybe its removed from remote server. Please contact us to more details', 'gutengeek' ) );
				}
				$name = isset( $_REQUEST['name'] ) ? sanitize_text_field( $_REQUEST['name'] ) : '';

				$args = apply_filters( 'gutengeek_import_template_args', [
					'method' => 'PATCH',
					'reject_unsafe_urls' => false,
					'headers' => [
						'Public-Key' => gutengeek_global_setting_name( 'public_key', '' ),
						'Token-Key' => gutengeek_global_setting_name( 'token_key', '' )
					],
					'body' => [
						'email' => get_option( 'admin_email' ),
						'siteurl' => get_option( 'siteurl' ),
					],
					'httpversion' => '1.0',
					'timeout' => 15
				] );
				// validate type for request
				$type = ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : '';
				$remote_server_url = sprintf( '%s%s', trailingslashit( gutengeek()->api->api_store_url ), $id );
				$request = wp_remote_post( $remote_server_url, $args );

				$header_status = wp_remote_retrieve_response_code( $request );
				$response = wp_remote_retrieve_body( $request );

				if ( 200 !== $header_status ) {
					$response = json_decode($response, true);
					$error_code = ! empty( $response['code'] ) ? $response['code'] : '';
					if ( ! empty( $response['message'] ) ) {
						throw new \Exception( $response['message'] );
					} else {
						throw new \Exception( __( 'Opp! Your request got too far. Please try again later.', 'gutengeek' ) );
					}
				} else {
					// try to save 'wp_block' post type
					$response = json_decode( $response, true );
					// create post
					if ( ! empty( $response['data'] ) && $name ) {
						if ( ! gutengeek_is_remote_item_saved( $id ) ) {
							$blocks = gutengeek_parse_blocks($response['data']);
							$content = '';
							foreach ( $blocks as $block ) {
								$content .= serialize_block($block);
							}
							$block = apply_filters( 'gutengeek_import_block_insert_args', [
								'post_title' => $name,
								'post_content' => $content,
								'post_status' => 'publish',
								'post_type' => 'wp_block'
							] );

							$local_id = wp_insert_post( wp_slash( $block ) );
							if ( ! is_wp_error( $local_id )) {
								update_post_meta( $local_id, '_gutengeek_imported_id', $id );
								update_post_meta( $local_id, '_gutengeek_imported_type', $type );
								$response['local_id'] = $local_id;
							}

							if ( ! empty($response['template_settings']) ) {
								// update meta key _gutengeek_post_settings
								update_post_meta( $local_id, '_gutengeek_post_settings', $response['template_settings'] );
							}
						}
						$response['id'] = $id;
					}
					wp_send_json_success( $response );
				}
			}
		} catch( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			]);
		}
	}

	/**
	 * ajax callback
	 *
	 * remove local template
	 */
	public static function remove_saved_local_template() {
		try {
			// throw new \Exception( __( 'You don\'t have permission to access this page', 'gutengeek' ) );
			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) ) {
				throw new \Exception( __( 'You don\'t have permission to remove this item', 'gutengeek' ) );
			}

			$local_id = ! empty( $_REQUEST['local_id'] ) ? absint( $_REQUEST['local_id'] ) : false;
			if ( ! $local_id ) {
				throw new \Exception( __( 'Item not found! :(', 'gutengeek' ) );
			}

			if ( $local_id ) {
				// delete
				if ( ! wp_delete_post( $local_id ) ) {
					throw new \Exception( __( 'Delete item failed. Try again later!', 'gutengeek' ) );
				}
				wp_send_json_success( [
					'local_id' => $local_id
				] );
			}
		} catch( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			]);
		}
	}

	public static function fetch_google_fonts() {
		if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'gutengeek-block-nonce' ) || ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [
				'items' => [],
			]);
		}

		$fonts_file = trailingslashit( GUTENGEEK_DIR ) . 'assets/libs/google-fonts.json';
		$items = [];
		if ( file_exists( $fonts_file ) ) {
			$items = json_decode( file_get_contents( $fonts_file ), true );
		}

		wp_send_json_success( [ 'items' => $items ] );
	}

}
