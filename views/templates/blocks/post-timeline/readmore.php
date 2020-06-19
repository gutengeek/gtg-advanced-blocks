<?php

defined( 'ABSPATH' ) || exit();

if ( ! isset($attributes['enableButton']) || !$attributes['enableButton'] ) {
	return;
}
$target = isset( $attributes['linkTarget'] ) && $attributes['linkTarget'] ? '_blank' : '_self';

$permalink = apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes );

do_action( "gutengeek_single_post_before_readmore_{$attributes['post_type']}", get_the_ID(), $attributes );
?>

	<div class="gutengeek-timeline-link_parent">
		<a class="gutengeek-timeline-link"
		   href="<?php echo esc_url( $permalink ) ?>"
		   target="<?php echo esc_attr( $target ); ?>"
		   rel=" noopener noreferrer"><?php echo esc_html( $attributes['readMoreText'] ); ?></a>
	</div>

<?php

do_action( "gutengeek_single_post_after_readmore_{$attributes['post_type']}", get_the_ID(), $attributes );
