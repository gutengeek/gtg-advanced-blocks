<?php

namespace Gtg_Advanced_Blocks\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class Api extends \WP_REST_Controller {

	/**
	 * namespace
	 *
	 * @var string string
	 */
	public $namespace = 'gutengeek/block/v1';

	/**
	 * endpoint
	 *
	 * @var string string
	 */
	public $rest_base;

	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Check if a given request has access to read a payment gateway.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}

	/**
	 * get collection params
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array();
	}

	/**
	 * get items
	 *
	 * @param \WP_REST_Request $request
	 * @return array|\WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		return array();
	}

	/**
	 * get licence api public key
	 *
	 * @return mixed|void
	 */
	public function get_licence_api_public_key() {
		return gtg_advanced_blocks()->licence->get_licence_public_key();
	}

	/**
	 * get licence api secret key
	 *
	 * @return mixed|void
	 */
	public function get_licence_api_secret_key() {
		return gtg_advanced_blocks()->licence->get_licence_secret_key();
	}

}
