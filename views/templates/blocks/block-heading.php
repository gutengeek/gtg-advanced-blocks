<?php
/**
 * The block heading template file
 *
 * @package WordPress
 * @subpackage gutengeek
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit();

$blockSubHeading = ! empty( $attributes['blockSubHeading'] ) ? $attributes['blockSubHeading'] : '';
$blockSubHeadingPosition = ! empty( $attributes['blockSubHeadingPosition'] ) ? $attributes['blockSubHeadingPosition'] : 'below_title';
$enableBlockSubHeading = ! empty( $attributes['enableBlockSubHeading'] ) ? $attributes['enableBlockSubHeading'] : false;
$blockHeadingTag = ! empty( $attributes['blockHeadingTag'] ) ? $attributes['blockHeadingTag'] : 'h3';
$enableBlockHeading = ! empty( $attributes['enableBlockHeading'] ) ? $attributes['enableBlockHeading'] : false;
$blockHeading = ! empty( $attributes['blockHeading'] ) ? $attributes['blockHeading'] : '';
$enableBlockDescription = ! empty( $attributes['enableBlockDescription'] ) ? $attributes['enableBlockDescription'] : false;
$blockHeadingDescription = ! empty( $attributes['blockHeadingDescription'] ) ? $attributes['blockHeadingDescription'] : '';
?>

<!-- Heading -->
<div class="gutengeek-block-heading-container">
	<div class="gutengeek-heading-wrap">
		<?php if ( $enableBlockSubHeading && $blockSubHeadingPosition === 'above_title' ) : ?>
			<div class="gutengeek-heading-sub-title">
				<?php printf( '%s', $blockSubHeading ) ?>
			</div>
		<?php endif; ?>
		<?php if ($enableBlockHeading) : ?>
			<<?php echo esc_attr($blockHeadingTag) ?> class="gutengeek-heading-text"><?php printf('%s', $blockHeading) ?></<?php echo esc_attr($blockHeadingTag) ?>>
		<?php endif ?>
		<?php if ( $enableBlockSubHeading && $blockSubHeadingPosition === 'below_title' ) : ?>
			<div class="gutengeek-heading-sub-title">
				<?php printf( '%s', $blockSubHeading ) ?>
			</div>
		<?php endif; ?>
		<?php if ( $enableBlockDescription ) : ?>
			<div class="gutengeek-heading-description">
				<?php printf( '%s', $blockHeadingDescription ) ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<!-- End Heading -->
