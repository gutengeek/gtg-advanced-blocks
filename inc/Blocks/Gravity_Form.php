<?php

namespace Gtg_Advanced_Blocks\Blocks;

use Gtg_Advanced_Blocks\Abstracts\Block;
use Gtg_Advanced_Blocks\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Gravity_Form extends Block implements InterfaceBlock {

	/**
	 * Gravity_Form constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'wp_ajax_gutengeek_gf_shortcode', [ $this, 'ajax_render_gf_shortcode' ] );
		add_action( 'wp_ajax_nopriv_gutengeek_gf_shortcode', [ $this, 'ajax_render_gf_shortcode' ] );
		// add_localize_scripts gutengeek_blocks_plugin
		add_filter( 'gutengeek_localize_scripts', [ $this, 'add_localize_scripts' ] );
	}


	/**
	 * @return bool
	 */
	public function visible() {
		return class_exists( '\GFForms' );
	}

	/**
	 * Get gravity forms with id
	 *
	 * @return array
	 */
	public function add_localize_scripts( $localizes ) {
		if ( ! $this->visible() ) {
			return $localizes;
		}

		$field_options   = [];
		$forms           = \RGFormsModel::get_forms( null, 'title' );
		$field_options[] = [
			'value' => -1,
			'label' => __( 'Select Form', 'gutengeek' ),
		];
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$field_options[] = [
					'value' => $form->id,
					'label' => $form->title,
				];
			}
		}
		if ( empty( $field_options ) ) {
			$field_options = [
				'-1' => __( 'You have not added any Gravity Forms yet.', 'gutengeek' ),
			];
		}

		$localizes['gf_forms'] = $field_options;

		return $localizes;
	}

	/**
	 * Render gravity form block backend via AJAX.
	 */
	public function ajax_render_gf_shortcode() {
		$id = absint( $_POST['formId'] );
		if ( $id && 0 != $id && -1 != $id ) {
			$data['html'] = do_shortcode( '[gravityforms id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Gravity Form.', 'gutengeek' ) . '</p>';
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
		return 'gravity-form';
	}

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes() {
		return [
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
		$blockAnimation = ! empty( $attributes['blockAnimation'] ) ? $attributes['blockAnimation'] : [];
		$blockId = 'gutengeek-block-' . $attributes['blockId'];
		$formId  = $attributes['formId'];
		$align   = isset( $attributes['align'] ) ? $attributes['align'] : '';

		$classname        = 'gutengeek-block-inner gutengeek-gf-align-' . $align . ' ';
		$classname        .= 'gutengeek-gf-radio-style-' . ( ! empty( $attributes['radioStyle'] ) ? $attributes['radioStyle'] : 1 );
		$classname        .= ' gutengeek-gf-checkbox-style-' . ( ! empty( $attributes['radioStyle'] ) ? $attributes['radioStyle'] : 1 );
		$class            = isset( $attributes['className'] ) ? $attributes['className'] : '';
		$enableTitleDesc  = ( isset( $attributes['enableTitleDesc'] ) ) ? $attributes['enableTitleDesc'] : true;
		$disableTitleDesc = '';

		$shapeTop = ! empty( $attributes['shapeTop'] ) ? $attributes['shapeTop'] : [];
		$shapeBottom = ! empty( $attributes['shapeBottom'] ) ? $attributes['shapeBottom'] : [];
		$background = ! empty( $attributes['blockBg'] ) ? $attributes['blockBg'] : [];
		$overlay = ! empty( $attributes['blockOverlayBg'] ) ? $attributes['blockOverlayBg'] : [];

		if ( ! $enableTitleDesc ) {
			$disableTitleDesc = ' title="false" description="false" ';
		}

		$enableAjax   = isset( $attributes['enableAjax'] ) ? 'true' : 'false';
		$formTabIndex = isset( $attributes['enableTabSupport'] ) ? $attributes['formTabIndex'] : 0;

		$blockAnimation = isset( $attributes['blockAnimation'], $attributes['blockAnimation']['name'] ) ? $attributes['blockAnimation'] : [];
		$animation = $blockAnimation ? _wp_specialchars( wp_json_encode( $blockAnimation ), ENT_QUOTES, 'UTF-8', true ) : '';
		ob_start();
		if ( $formId && 0 != $formId && -1 != $formId ) {
			?>
			<div id="<?php echo esc_attr( $blockId ); ?>" class="<?php echo esc_attr( $class ) ?> gutengeek-block-container gutengeek-block-gf gutengeek-gf-container align<?php echo esc_attr( $align ); ?>"<?php echo sprintf( '%s', $animation ? ' data-gutengeek-animation="'.$animation.'"' : '' ) ?>>

		     	<?php if ( isset($shapeTop) && ! empty( $shapeTop['shape'] )) : ?>
					<div class="gutengeek-shape-divider gutengeek-shape-top"><?php printf( '%s', gtg_render_shape_background( $shapeTop['shape'] ) ) ?></div>
				<?php endif; ?>

				<?php if ( isset( $overlay, $overlay['source'] ) ): ?>
					<div class="gutengeek-block__overlay"></div>
				<?php endif; ?>

				<div class="gutengeek-block-inner">
					<div class="<?php echo esc_attr( $classname ); ?>">
						<?php echo do_shortcode( '[gravityforms id="' . $formId . '" ' . $disableTitleDesc . ' ajax="' . $enableAjax . '" tabindex="' . $formTabIndex . '"]' ); ?>
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
