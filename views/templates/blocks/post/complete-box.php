<?php

defined( 'ABSPATH' ) || exit();

if ( !( isset( $attributes['linkBox'] ) && $attributes['linkBox'] ) ) {
	return;
}
$target = isset( $attributes['newTab'] ) && $attributes['newTab'] ? '_blank' : '_self';
?>

	<a class="gutengeek-post__link-complete-box"
	   href="<?php echo apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ); ?>"
	   target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"></a>

<?php
