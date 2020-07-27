<?php

namespace Gtg_Advanced_Blocks;

class Licence {

	/**
	 * @access private
	 * @var string
	 */
	private $licence_key;

	/**
	 * @access $private
	 * @var string
	 */
	private $licence_public_key;

	/**
	 * @access $private
	 * @var string
	 */
	private $licence_secret_key;

	/**
	 * user
	 *
	 * @var null | object
	 */
	public $user;

	/**
	 * Licence constructor.
	 */
	public function __construct() {
		$this->licence_public_key = $this->get_licence_public_key();
		$this->licence_secret_key = $this->get_licence_secret_key();
		$this->user = $this->get_user();
	}

	/**
	 * get licence public key
	 *
	 * @return mixed|void
	 */
	public function get_licence_public_key() {
		return gtg_get_option( '_gutengeek_licence_public_key' );
	}

	/**
	 * get public secret key
	 *
	 * @return mixed|void
	 */
	public function get_licence_secret_key() {
		return gtg_get_option( '_gutengeek_licence_secret_key' );
	}

	/**
	 * get user from remote server
	 *
	 * @access public
	 * @return object|void|null
	 * @since 1.0.0
	 */
	public function get_user() {
		if ( !$this->user ) {
			return $this->user = $this->_get_user();
		}
		return $this->user;
	}

	/**
	 *
	 */
	private function _get_user() {

	}

}
