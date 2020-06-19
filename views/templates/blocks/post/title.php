<?php

defined( 'ABSPATH' ) || exit();

$target = ! empty($attributes['newTab']) && $attributes['newTab'] ? '_blank' : '_self';
do_action( "gutengeek_single_post_before_title_{$attributes['post_type']}", get_the_ID(), $attributes );

$tileTag = ! empty($attributes['titleTag']) ? $attributes['titleTag'] : 'h3';
$titleTag = apply_filters( 'gutengeek_post_title_tag', $tileTag, $attributes, get_the_ID() );
?>
	<<?php echo sprintf( '%s', $titleTag ); ?> class="gutengeek-post__title">
	<a href="<?php echo apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ); ?>"
	   target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"><?php the_title(); ?></a>
	</<?php echo sprintf( '%s', $titleTag ); ?>>
<?php
do_action( "gutengeek_single_post_after_title_{$attributes['post_type']}", get_the_ID(), $attributes );
