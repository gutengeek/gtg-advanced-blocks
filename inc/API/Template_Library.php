<?php

namespace Gtg_Advanced_Blocks\API;
use Gtg_Advanced_Blocks\Abstracts\Api;

class Template_Library extends Api {

	/**
	 * endpoint
	 *
	 * @var string string
	 */
	public $rest_base = 'templates';

	/**
	 * register rest api route
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				)
			)
		);
	}

	/**
	 * get items
	 *
	 * @param \WP_REST_Request $request
	 * @return array|mixed|\WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$response = array();
		$request = wp_safe_remote_request('https://jsonplaceholder.typicode.com/posts');
		if ( 200 === wp_remote_retrieve_response_code( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ), true );
		}

		return rest_ensure_response( $response );
	}

}
