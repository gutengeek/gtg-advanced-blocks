<?php

namespace GutenGeek\Blocks;

use GutenGeek\Abstracts\Block;
use GutenGeek\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Mailchimp extends Block implements InterfaceBlock {

	/**
	 * Mailchimp constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'wp_ajax_gutengeek_mailchimp_shortcode', [ $this, 'ajax_render_mailchimp_shortcode' ] );
		add_action( 'wp_ajax_nopriv_gutengeek_mailchimp_shortcode', [ $this, 'ajax_render_mailchimp_shortcode' ] );
		add_filter( 'gutengeek_localize_scripts', [ $this, 'add_localize_scripts' ] );
	}

	/**
	 * @return bool
	 */
	public function visible() {
		return function_exists( '_mc4wp_load_plugin' );
	}

	/**
	 * Get Mailchimp forms with id
	 *
	 * @return array
	 */
	public function add_localize_scripts( $localizes ) {
		if ( ! $this->visible() ) {
			return $localizes;
		}

		$field_options   = [];
		$forms           = mc4wp_get_forms();
		$field_options[] = [
			'value' => -1,
			'label' => __( 'Select Form', 'gutengeek' ),
		];
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$field_options[] = [
					'value' => $form->ID,
					'label' => $form->name,
				];
			}
		}
		if ( empty( $field_options ) ) {
			$field_options = [
				'-1' => __( 'You have not added any Mailchimp Forms yet.', 'gutengeek' ),
			];
		}

		$localizes['mailchimp_forms'] = $field_options;

		return $localizes;
	}

	/**
	 * Render gravity form block backend via AJAX.
	 */
	public function ajax_render_mailchimp_shortcode() {
		$id = absint( $_POST['formId'] );
		if ( $id && 0 != $id && -1 != $id ) {
			$data['html'] = do_shortcode( '[mc4wp_form id="' . $id . '"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Mailchimp Form.', 'gutengeek' ) . '</p>';
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
		return 'mailchimp';
	}

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes() {
		return [
			'is_active' => class_exists( '\MC4WP_Form_Manager' ),
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
		$formId  = ! empty($attributes['formId']) ? $attributes['formId'] : 0;
		$align  = ! empty( $attributes['align'] ) ? ' align' . $attributes['align'] : '';
		$style = isset($attributes['style']) ? $attributes['style'] : 'vertical';

		$classname = $align ? 'gutengeek-mailchimp-align-' . $align . ' ' : '';
		$classname .= 'gutengeek-mailchimp-radio-style-' . ( ! empty( $attributes['radioStyle'] ) ? $attributes['radioStyle'] : 1 );
		$classname .= ' gutengeek-mailchimp-checkbox-style-' . ( ! empty( $attributes['radioStyle'] ) ? $attributes['radioStyle'] : 1 );
		$class = isset( $attributes['className'] ) ? $attributes['className'] : '';

		$classnames = [
			'gutengeek-block-container',
			'gutengeek-block',
			'wp-block-gutengeek-mailchimp',
			'gutengeek-mailchimp-wrap',
			'is-style-' . $style,
			$class,
			$align
		];

		$shapeTop = ! empty( $attributes['shapeTop'] ) ? $attributes['shapeTop'] : [];
		$shapeBottom = ! empty( $attributes['shapeBottom'] ) ? $attributes['shapeBottom'] : [];
		$background = ! empty( $attributes['blockBg'] ) ? $attributes['blockBg'] : [];
		$overlay = ! empty( $attributes['blockOverlayBg'] ) ? $attributes['blockOverlayBg'] : [];
		ob_start();
		if ( $formId && 0 != $formId && -1 != $formId ) { ?>
			<div class="<?php echo esc_attr( implode( ' ', $classnames ) ) ?>" id="<?php echo esc_attr( $blockId ); ?>">
		     	<?php if ( isset($shapeTop) && ! empty( $shapeTop['shape'] )) : ?>
					<div class="gutengeek-shape-divider gutengeek-shape-top"><?php printf( '%s', gutengeek_render_shape_background( $shapeTop['shape'] ) ) ?></div>
				<?php endif; ?>

				<?php if ( isset( $overlay, $overlay['source'] ) ): ?>
					<div class="gutengeek-block__overlay"></div>
				<?php endif; ?>

				<div class="gutengeek-block-inner">
					<div class="<?php echo esc_attr( $classname ); ?>">
						<?php echo do_shortcode( '[mc4wp_form id="' . $formId . '"]' ); ?>
					</div>
				</div>

				<?php if ( isset($shapeBottom) && ! empty( $shapeBottom['shape'] )) : ?>
					<div class="gutengeek-shape-divider gutengeek-shape-bottom"><?php printf( '%s', gutengeek_render_shape_background( $shapeBottom['shape'] ) ) ?></div>
				<?php endif; ?>
			</div>
			<?php
		}

		return ob_get_clean();
	}
}
