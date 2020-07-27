<?php

defined( 'ABSPATH' ) || exit();

if ( !isset( $attributes['enableButton'] ) || !$attributes['enableButton'] ) {
	return;
}

$target = !empty( $attributes['newTab'] ) && $attributes['newTab'] ? '_blank' : '_self';

$cta_text = !empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Read More', 'gutengeek' );

$permalink = apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes );

do_action( "gutengeek_single_post_before_readmore_{$attributes['post_type']}", get_the_ID(), $attributes );
?>

	<div class="gutengeek-post__cta">
		<a class="gutengeek-post__link gutengeek-text-link gutengeek-button gutengeek-button--<?php echo esc_attr(!empty( $attributes['buttonStyle'] ) ? $attributes['buttonStyle'] : 'no-style') ?><?php echo (!empty( $attributes['buttonSize'] ) ? esc_attr( ' gutengeek-button-size-' . $attributes['buttonSize'] ) : '') ?>"
		   href="<?php echo esc_url( $permalink ); ?>"
		   target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer">
			<?php printf( '%s', $cta_text ); ?>
		</a>
	</div>

<?php

do_action( "gutengeek_single_post_after_readmore_{$attributes['post_type']}", get_the_ID(), $attributes );
