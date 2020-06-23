<?php

defined( 'ABSPATH' ) || exit();

$target = ( isset( $attributes['linkTarget'] ) && ( true == $attributes['linkTarget'] ) ) ? '_blank' : '_self';

$headingTag = ! empty($attributes['headingTag']) ? $attributes['headingTag'] : 'h3';
global $post;

?>

<div class="gutengeek-timeline-heading-text">
	<?php do_action( "gutengeek_single_post_before_title_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
		<<?php echo sprintf( $headingTag ); ?> class="gutengeek-timeline-heading" >
		<a href="<?php echo apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ); ?>"
		   target="<?php echo esc_attr( $target ); ?>"
		   rel="noopener noreferrer"><?php ( '' !== get_the_title( $post->ID ) ) ? the_title() : _e( 'Untitled', 'gutengeek' ); ?></a>
		</<?php echo sprintf( $headingTag ); ?>>
	<?php do_action( "gutengeek_single_post_after_title_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
</div>
