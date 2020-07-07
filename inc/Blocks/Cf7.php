<?php

namespace Gtg_Advanced_Blocks\Blocks;

use Gtg_Advanced_Blocks\Abstracts\Block;
use Gtg_Advanced_Blocks\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Cf7 extends Block implements InterfaceBlock {

	/**
	 * Cf7 constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'register_post_type_args', [ $this, 'add_cpts_to_api' ], 10, 2 );
		add_action( 'wp_ajax_gutengeek_cf7_shortcode', [ $this, 'ajax_cf7_shortcode' ] );
		add_action( 'wp_ajax_nopriv_gutengeek_cf7_shortcode', [ $this, 'ajax_cf7_shortcode' ] );
		// add_localize_scripts gutengeek_blocks_plugin
		add_filter( 'gutengeek_localize_scripts', [ $this, 'add_localize_scripts' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );
	}

	/**
	 * @return bool
	 */
	public function visible() {
		return class_exists( '\WPCF7_ContactForm' );
	}

	/**
	 * Function to integrate CF7 Forms.
	 *
	 * @since 1.0.0
	 */
	public function add_localize_scripts( $localizes ) {
		if ( ! $this->visible() ) {
			return $localizes;
		}
		$field_options = [];
		$args = [
			'post_type' => 'wpcf7_contact_form',
			'posts_per_page' => -1,
		];
		$forms = get_posts( $args );
		$field_options[0] = [
			'value' => -1,
			'label' => __( 'Select Form', 'gutengeek' ),
		];
		if ( $forms ) {
			foreach ( $forms as $form ) {
				$field_options[] = [
					'value' => $form->ID,
					'label' => $form->post_title,
				];
			}
		}
		if ( empty( $field_options ) ) {
			$field_options = [
				'-1' => __( 'You have not added any Contact Form 7 yet.', 'gutengeek' ),
			];
		}

		$localizes['cf7_forms'] = $field_options;

		return $localizes;
	}

	/**
	 * enqueue block assets
	 */
	public function enqueue_block_assets() {
		if ( ! $this->visible() ) {
			return;
		}
		if ( !wp_script_is( 'contact-form-7', 'enqueued' ) ) {
			wp_enqueue_script( 'contact-form-7' );
		}

		if ( !wp_script_is( ' wpcf7-admin', 'enqueued' ) ) {
			wp_enqueue_script( 'wpcf7-admin' );
		}
	}

	/**
	 * add 'wpcf7_contact_form' avaiable on rest api
	 *
	 * @param $args
	 * @param $post_type
	 * @return mixed
	 */
	public function add_cpts_to_api( $args, $post_type ) {
		if ( 'wpcf7_contact_form' === $post_type ) {
			$args['show_in_rest'] = true;
		}

		return $args;
	}

	/**
	 * ajax contact form 7 render block backend
	 */
	public function ajax_cf7_shortcode() {
		$id = absint( $_POST['formId'] );
		if ( $id && 0 != $id && -1 != $id ) {
			$data['html'] = do_shortcode( '[contact-form-7 id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Contact Form 7.', 'gutengeek' ) . '</p>';
		}
		wp_send_json_success( $data );
	}

	/**
	 * register assets
	 *
	 * @return bool|mixed|void
	 */
	public function register_assets() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
	}

	/**
	 * Block id
	 *
	 * @return mixed|string
	 */
	public function set_slug() {
		return 'cf7';
	}

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes() {
		return [
			'is_active'       => $this->visible(),
			'render_callback' => [ $this, 'render_callback' ],
		];
	}

	/**
	 * render callback server rendering
	 *
	 * @param $attributes
	 * @return false|string
	 */
	public function render_callback( $attributes ) {
		$blockId = 'gutengeek-block-' . $attributes['blockId'];
		$formId  = $attributes['formId'];
		$textAlign   = isset( $attributes['textAlign'] ) ? $attributes['textAlign'] : '';

		$classname = 'gutengeek-cf7--align-' . $textAlign . ' ';
		$classname .= 'gutengeek-cf7--radio-style-' . (! empty($attributes['radioStyle']) ? $attributes['radioStyle'] : 1);
		$class = isset( $attributes['className'] ) ? $attributes['className'] : '';
		$outerWrap = [
			'gutengeek-block-container',
			'gutengeek-block',
			'wp-block-gutengeek-cf7',
			'gutengeek-cf7--outer-wrap',
			! empty($attributes['align']) ? ' align' . $attributes['align'] : '',
			$class
		];

		$shapeTop = ! empty( $attributes['shapeTop'] ) ? $attributes['shapeTop'] : [];
		$shapeBottom = ! empty( $attributes['shapeBottom'] ) ? $attributes['shapeBottom'] : [];
		$background = ! empty( $attributes['blockBg'] ) ? $attributes['blockBg'] : [];
		$overlay = ! empty( $attributes['blockOverlayBg'] ) ? $attributes['blockOverlayBg'] : [];

		$blockAnimation = isset( $attributes['blockAnimation'], $attributes['blockAnimation']['name'] ) ? $attributes['blockAnimation'] : [];
		$animation = $blockAnimation ? _wp_specialchars( wp_json_encode( $blockAnimation ), ENT_QUOTES, 'UTF-8', true ) : '';
		ob_start();
		if ( $formId && 0 != $formId && -1 != $formId ) {
			?>
			<div id="<?php echo esc_attr( $blockId ); ?>" class="<?php echo esc_attr( implode( ' ', $outerWrap ) ) ?>"<?php echo sprintf( '%s', $animation ? ' data-gutengeek-animation="'.$animation.'"' : '' ) ?>>

			 	<?php if ( isset($shapeTop) && ! empty( $shapeTop['shape'] )) : ?>
					<div class="gutengeek-shape-divider gutengeek-shape-top"><?php printf( '%s', gtg_render_shape_background( $shapeTop['shape'] ) ) ?></div>
				<?php endif; ?>

				<?php if ( isset( $overlay, $overlay['source'] ) ): ?>
					<div class="gutengeek-block__overlay"></div>
				<?php endif; ?>

				<div class="gutengeek-block-inner">
					<div class="<?php echo esc_attr( $classname ); ?>">
						<?php echo do_shortcode( '[contact-form-7 id="' . $formId . '"]' ); ?>
					</div>
				</div>

				<?php if ( isset($shapeBottom) && ! empty( $shapeBottom['shape'] )) : ?>
					<div class="gutengeek-shape-divider gutengeek-shape-bottom"><?php printf( '%s', gtg_render_shape_background( $shapeBottom['shape'] ) ) ?></div>
				<?php endif; ?>
			</div>
			<?php
		}

		return ob_get_clean();
	}
}
