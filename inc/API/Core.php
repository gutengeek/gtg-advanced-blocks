<?php

namespace GutenGeek\API;

use GutenGeek\Abstracts\Api;

/**
 * Main API for editor
 *
 * Class Core
 * @package GutenGeek\API
 */
class Core extends Api {

	/**
	 * endpoint
	 *
	 * @var string string
	 */
	public $rest_base = 'post';

	/**
	 * register rest api route
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods' => \WP_REST_Server::EDITABLE,
					'callback' => [ $this, 'post_edit_action' ],
					'permission_callback' => [ $this, 'post_edit_action_permissions_check' ],
				]
			]
		);
	}

	/**
	 * post_edit_action
	 *
	 * @param \WP_REST_Request $request
	 * @return array|mixed|\WP_Error|\WP_REST_Response
	 */
	public function post_edit_action( $request ) {
		try {
			if ( !function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();
			global $wp_filesystem;

			// before process storage post
			do_action( 'gutengeek_before_storage_post_api', $request );

			$post_id = $request->get_param( 'post_id' );
			$meta_data = $request->get_param( 'meta' );
			$css_string = isset( $meta_data['_gutengeek_css'] ) ? $meta_data['_gutengeek_css'] : '';
			$post_settings = ! empty( $meta_data['_gutengeek_post_settings'] ) ? $meta_data['_gutengeek_post_settings'] : false;

			update_post_meta( $post_id, '_gutengeek_css', $css_string );
			$css_file = gutengeek_get_static_css_file_path( $post_id );
			$wp_filesystem->put_contents( $css_file, $css_string );

			if ( $post_settings ) {
				update_post_meta( $post_id, '_gutengeek_post_settings', $post_settings );
			}

			// storaged
			do_action( 'gutengeek_storaged_post_api', $request );

			$response = apply_filters( 'gutengeek_storaged_post_response_api', [
				'status' => true,
			] );
		} catch ( \Exception $e ) {
			$response = [ 'status' => false, 'message' => __( 'Generate post css failed', 'gutengeek' ) ];
		}

		return rest_ensure_response( $response );
	}

	/**
	 * determine the current user can edit the post
	 *
	 * @return bool
	 */
	public function post_edit_action_permissions_check() {
		return current_user_can( 'edit_posts' );
	}


}
