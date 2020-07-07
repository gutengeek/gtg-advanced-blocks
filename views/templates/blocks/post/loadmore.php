<?php

defined( 'ABSPATH' ) || exit();

$btn_classes = [
	'gutengeek-post__loadmore-button',
	'gutengeek-button',
	isset($attributes['pagiLoadmoreType']) && $attributes['pagiLoadmoreType'] ? 'gutengeek-button-' . $attributes['pagiLoadmoreType'] : '',
	isset($attributes['pagiLoadmoreWidth']) && $attributes['pagiLoadmoreWidth'] ? 'gutengeek-button-width-' . $attributes['pagiLoadmoreWidth'] : '',
	isset($attributes['pagiLoadmoreSize']) && $attributes['pagiLoadmoreSize'] ? 'gutengeek-button-size-' . $attributes['pagiLoadmoreSize'] : '',
];

$term = isset( $_GET['term'] ) && $_GET['term'] ? sanitize_text_field( $_GET['term'] ) : '';
?>

<div class="gutengeek-post__loadmore">
	<div class="gutengeek-post__loadmore-wrap gutengeek-button__wrapper">
		<a href="#" class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $btn_classes ) ) ); ?>" data-term="<?php echo esc_attr( $term ); ?>" data-paged="1">
			<?php if ( isset($attributes['pagiLoadmoreEnableIconBefore'], $attributes['pagiLoadmoreIconBefore'] ) && $attributes['pagiLoadmoreEnableIconBefore'] && $attributes['pagiLoadmoreIconBefore'] ) : ?>
				<span class="gutengeek-button__icon-wrapper gutengeek-icon-before"></span>
			<?php endif; ?>

			<?php if ( isset($attributes['pagiLoadmoreLabel']) && $attributes['pagiLoadmoreLabel'] ) : ?>
				<span class="gutengeek-button__text">
					<?php echo esc_html( $attributes['pagiLoadmoreLabel'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( isset($attributes['pagiLoadmoreEnableIconAfter'], $attributes['pagiLoadmoreIconAfter'] ) && $attributes['pagiLoadmoreEnableIconAfter'] && $attributes['pagiLoadmoreIconAfter'] ) : ?>
				<span class="gutengeek-button__icon-wrapper gutengeek-icon-after"></span>
			<?php endif; ?>
		</a>
	</div>
</div>
