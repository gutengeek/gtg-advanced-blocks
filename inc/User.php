<?php
/**
 * User.
 *
 * @package Gtg_Advanced_Blocks
 */

namespace Gtg_Advanced_Blocks;

class User {

	public static $instance = null;

	public $data = [];

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * get licence public key
	 *
	 * @return mixed|void
	 */
	public function get_licence_public_key() {
		return gtg_get_setting( 'public_key' );
	}

	/**
	 * get public secret key
	 *
	 * @return mixed|void
	 */
	public function get_licence_secret_key() {
		return gtg_get_setting( 'token_key' );
	}

	/**
	 * fetch user info by public key and token key
	 *
	 * @param $public_key
	 * @param $token_key
	 * @return array | exception
	 */
	public function fetch_user_info( $public_key = '', $token_key = '' ) {
		$args = [
			'method' => 'GET',
			'reject_unsafe_urls' => false,
			'headers' => [
				'Public-Key' => $public_key ? $public_key : $this->get_licence_public_key(),
				'Token-Key' => $token_key ? $token_key : $this->get_licence_secret_key()
			],
			'body' => [
				'email' => get_option( 'admin_email' ),
				'siteurl' => get_option( 'siteurl' ),
			],
			'httpversion' => '1.0',
			'timeout' => 30
		];
		$args = apply_filters( 'gutengeek_fetch_user_request_args', $args );

		// api request
		if ( defined( 'WP_DEBUG' ) ) {
			// add_filter( 'http_request_args', [ __CLASS__, 'remove_unsfafe_url' ], 10, 2 );
		}

		$request = wp_remote_get( gtg_advanced_blocks()->api->api_user_info_url, $args );
		if ( defined( 'WP_DEBUG' ) ) {
			// remove_filter( 'http_request_args', [ __CLASS__, 'remove_unsfafe_url' ], 10, 2 );
		}
		$header_status = wp_remote_retrieve_response_code( $request );
		$response = wp_remote_retrieve_body( $request );
		$data = [];
		if ( 200 === $header_status ) {
			if ( wp_json_encode($response) ) {
				$data = json_decode( $response, true );
				if ( ! $data ) {
					throw new \Exception( __( 'User not found.', 'gutengeek' ) );
				}

				if ( ! empty( $data['user'] ) ) {
					$data['user']['publicKey'] = $this->get_licence_public_key();
					$data['user']['tokenKey'] = $this->get_licence_secret_key();
				}
			} else {
				throw new \Exception( __( 'User not found.', 'gutengeek' ) );
			}
		} else {
			$data = json_decode($response, true);
			if ( ! empty( $response['message'] ) ) {
				throw new \Exception( $response['message'] );
			} else {
				throw new \Exception( __( 'Opp! Your request got too far. Please try again later.', 'gutengeek' ) );
			}
		}

		return apply_filters( 'gutengeek_fetch_remote_user_results', $data, $args );
	}

	/**
	 * set user data
	 */
	public function set_user_data( $data = [] ) {
		foreach ( $data as $name => $value ) {
			$this->data[$name] = $value;
		}

	}

	/**
	 * get user data
	 */
	public function get_user_data() {
		return apply_filters( 'gutengeek_user_remote_data', $this->data );
	}

}
