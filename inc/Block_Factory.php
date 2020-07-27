<?php
/**
 * WPOPAL Block Factory.
 *
 * @package Gtg_Advanced_Blocks
 */

namespace Gtg_Advanced_Blocks;

use Gtg_Advanced_Blocks\Abstracts\Block as AbstractBlock;
use Gtg_Advanced_Blocks\Blocks\Gravity_Form;
use Gtg_Advanced_Blocks\Blocks\Mailchimp;
use Gtg_Advanced_Blocks\Blocks\Post_Carousel;
use Gtg_Advanced_Blocks\Blocks\Post_Grid;
use Gtg_Advanced_Blocks\Blocks\Post_Masonry;
use Gtg_Advanced_Blocks\Blocks\Post_Timeline;
use Gtg_Advanced_Blocks\Blocks\Cf7;

defined( 'ABSPATH' ) || exit();

class Block_Factory {

	/**
	 * @var Block_Factory | null
	 */
	public static $instance = null;

	/**
	 * block classes
	 *
	 * @var array|mixed|void
	 */
	private $block_classes = [];

	/**
	 * blocks list
	 *
	 * @var array|mixed|void
	 */
	public $blocks = [];

	/**
	 * blocks attributes
	 *
	 * @var array
	 */
	public $block_attributes = [];

	/**
	 * @return Block_Factory|null
	 */
	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
		$this->block_classes = $this->init_block_classes();

		// blocks
		$this->blocks = $this->get_blocks();

		/**
		 * progress submit form
		 */
		add_action( 'wp_ajax_gutengeek_form_submit', [ $this, 'progress_custom_form' ] );
		add_action( 'gutengeek_process_custom_form_validated', [ $this, 'custom_form_validated' ], 10, 2 );
	}

	/**
	 * init blocks classes
	 *
	 * @return mixed|void
	 */
	private function init_block_classes() {
		return apply_filters( 'gutengeekblock_init_block_classes', [
			new Cf7(),
			new Gravity_Form(),
			new Mailchimp(),
			new Post_Carousel(),
			new Post_Timeline(),
			new Post_Masonry(),
			new Post_Grid(),
		], $this );
	}

	/**
	 * get blocks list
	 *
	 * @return mixed|void
	 */
	public function get_blocks() {
		if ( $this->blocks ) {
			return $this->blocks;
		}

		$blocks = [];
		foreach ( $this->block_classes as $key => $block ) {
			$blocks[ $block->get_slug() ] = $block;
		}

		return apply_filters( 'gutengeek_blocks_list', $blocks );
	}

	/**
	 * get blocks list attributes
	 *
	 * @return mixed|void
	 */
	public function get_block_attributes() {
		foreach ( $this->get_blocks() as $slug => $block ) {
			$unique_id = $block->get_block_prefix() . '/' . $slug;
			$this->block_attributes[ $unique_id ] = $block->get_attributes();
		}

		return apply_filters( 'gutengeek_blocks_list_attributes', $this->block_attributes );
	}

	/**
	 * get block
	 *
	 * @param null $block
	 * @return mixed
	 */
	public function get( $block = null ) {
		$blocks = $this->get_blocks();
		if ( isset( $blocks[ $block ] ) && $blocks[ $block ] instanceof AbstractBlock ) {
			return $blocks[ $block ];
		}
	}

	/**
	 * init hooks
	 */
	public function init_hooks() {
		// register block categories
		add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2 );
		add_action( 'init', [ $this, 'register_block_types' ] );
	}

	/**
	 * Gutenberg block category for WPOPAL.
	 *
	 * @param $categories Block categories.
	 * @param $post Post object.
	 * @return array
	 */
	function register_block_category( $categories, $post ) {
		return array_merge(
			$categories, [
				[
					'slug' => 'gutengeek',
					'title' => __( 'GutenGeek', 'gutengeek' ),
				],
			]
		);
	}

	/**
	 * register block type if has 'editor_script' || 'editor_style' || 'script' || 'style'
	 */
	public function register_block_types() {
		if ( !function_exists( 'register_block_type' ) ) {
			return;
		}

		foreach ( $this->get_blocks() as $slug => $block ) {
			$attributes = $block->get_attributes();
			// here we need to determine the use want to load single asset file each blocks
			if ( !gtg_get_option( '_gutengeekblock_bundle_assets' ) && isset( $attributes['editor_script'] )
				|| isset( $attributes['script'] )
				|| isset( $attributes['editor_style'] )
				|| isset( $attributes['style'] )
			) {
				// register assets
				$block->register_assets();
			}
			// register block type
			register_block_type( $block->get_block_prefix() . '/' . $slug, $attributes );
		}
	}

	/**
	 * process submit custom form
	 *
	 * ajax call back
	 */
	public function progress_custom_form() {
		try {
			if ( ! isset($_POST['_nonce']) || ! wp_verify_nonce( $_POST['_nonce'], 'gutengeek-form-nonce' ) ) {
				throw new \Exception( __('You don\'t have permission.', 'gutengeek') );
			}

			if ( isset($_POST['recaptcha']) ) {
				// validate recaptcha
				$captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
				$secret_key = isset($_POST['recaptcha-secret-key']) ? sanitize_text_field( $_POST['recaptcha-secret-key'] ) : '';
				$verify = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha}");

				if ( ! is_array($verify) || !isset($verify['body']) ) {
					throw new \Exception( __('Captcha validate failed', 'gutengeek') );
				}

				$verified = json_decode($verify['body']);
				if (! $verified->success) {
					throw new \Exception( __('Captcha validation error', 'gutengeek') );
				}
			}

			$form_settings = ! empty($_POST['form-settings']) ? json_decode(base64_decode(sanitize_text_field($_POST['form-settings'])), true) : [];
			$fields = isset($form_settings['fields']) ? $form_settings['fields'] : [];
			$message_success = ! empty($_POST['success-message']) ? sanitize_text_field( base64_decode($_POST['success-message']) ) : __('Email submited successful', 'gutengeek');
			$message_error = ! empty($_POST['error-message']) ? sanitize_text_field( base64_decode($_POST['error-message']) ) : __('Oops! Email submitted failed', 'gutengeek');
			$message_required = ! empty($_POST['error-required']) ? sanitize_text_field( base64_decode($_POST['error-required']) ) : __('This field is required', 'gutengeek');
			// validate form settings
			$params = $_POST;
			foreach ( $fields as $field ) {
				$name = ! empty($field['name']) ? sanitize_text_field( $field['name'] ) : '';
				$type = ! empty($field['type']) ? sanitize_text_field( $field['type'] ) : '';
				$value = '';
				switch( $type ) {
					case 'text':
					case 'tel':
					case 'select':
					case 'textarea':
						$value = ! empty($_POST[$name]) ? sanitize_text_field( $_POST[$name] ) : '';
						break;
					case 'email':
						$value = ! empty($_POST[$name]) ? sanitize_email( $_POST[$name] ) : '';
						break;
					case 'number':
						$value = ! empty($_POST[$name]) ? sanitize_text_field( $_POST[$name] ) : '';
						break;
					default:
						$value = '';
						break;
				}
				$params[$name] = $value;
			}

			$actions = ! empty($_POST['actions']) ? array_map( 'sanitize_text_field', $_POST['actions'] ) : [];

			// trigger action
			do_action( 'gutengeek_process_custom_form_validated', $actions, $params );

			wp_send_json([
				'status' => true,
				'message' => $message_success
			]);
		} catch(\Exception $e) {
			wp_send_json([
				'status' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	/**
	 * validated success
	 *
	 * @param $params
	 */
	public function custom_form_validated( $actions = [], $params = [] ) {
		// send an email
		if ( ! in_array( 'email', $actions ) ) {
			return;
		}

		try {
			$email_from = ! empty( $params['email-from'] ) ? sanitize_text_field( $params['email-from'] ) : '';
			$email_from = $email_from ? base64_decode( $email_from ) : '';
			$email_reply = ! empty( $params['email-reply'] ) ? sanitize_text_field( $params['email-reply'] ) : '';
			$email_reply = $email_reply ? base64_decode( $email_reply ) : '';
			$email_subject = ! empty( $params['email-subject'] ) ? sanitize_text_field( $params['email-subject'] ) : '';
			$email_subject = $email_subject ? base64_decode($email_subject) : '';
			$email_recipients = ! empty( $params['email-recipients'] ) ? $params['email-recipients'] : '';
			$email_recipients = $email_recipients ? base64_decode( $email_recipients ) : '';
			$email_cc = ! empty( $params['email-cc'] ) ? sanitize_text_field( $params['email-cc'] ) : '';
			$email_cc = $email_cc ? base64_decode( $email_cc ) : '';
			$email_bcc = ! empty( $params['email-bcc'] ) ? sanitize_text_field( $params['email-bcc'] ) : '';
			$email_bcc = $email_bcc ? base64_decode($email_bcc) : '';
			$email_body = ! empty( $params['email-body'] ) ? sanitize_text_field( $params['email-body'] ) : '';
			$email_body = $email_body ? base64_decode( $email_body ) : '';

			// from
			$from_name = $from_email = '';
			if ( $email_from ) {
				$email_from = explode( ':', $email_from );
				$from_name = isset($email_from[0]) ? trim($email_from[0]) : '';
				$from_email = isset($email_from[1]) ? trim($email_from[1]) : '';
			}

			// reply
			$reply_name = $reply_email = '';
			if ( $email_reply ) {
				$email_reply = explode( ':', $email_reply );
				$reply_name = isset($email_reply[0]) ? trim($email_reply[0]) : '';
				$reply_email = isset($email_reply[1]) ? trim($email_reply[1]) : '';
			}

			foreach ( $params as $name => $value ) {
				$value = is_array($value) ? implode(', ', $value) : $value;
				$email_subject = str_replace('{{' . $name . '}}', $value, $email_subject);
				$email_body = str_replace('{{' . $name . '}}', $value, $email_body);
				$from_name = str_replace('{{' . $name . '}}', $value, $from_name);
				$from_email = str_replace('{{' . $name . '}}', $value, $from_email);
				$reply_name = str_replace('{{' . $name . '}}', $value, $reply_name);
				$reply_email = str_replace('{{' . $name . '}}', $value, $reply_email);
			}

			// headers
			$headers = [];
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
			$headers[] = 'Reply-To: ' . $reply_name . ' <' . $reply_email . '>';
			$headers[] = 'Cc: <' . $email_cc . '>';
			$headers[] = 'Bcc: <' . $email_bcc . '>';

			$send = wp_mail( $email_recipients, $email_subject, $email_body, $headers );
		} catch( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	/**
	 * magic method
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		$blocks = $this->blocks;
		if ( isset( $blocks[ $name ] ) && $blocks[ $name ] instanceof AbstractBlock ) {
			return $blocks[ $name ];
		}
	}

}
