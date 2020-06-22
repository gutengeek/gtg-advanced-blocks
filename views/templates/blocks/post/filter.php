<?php

defined( 'ABSPATH' ) || exit();
if ( ! isset( $attributes['taxonomyType'] ) || ! $attributes['taxonomyType'] ) {
	return;
}

?>

<div class="gutengeek-post__filter">
	<form class="gutengeek-post__filter-tabs <?php echo esc_attr( $attributes['filterAjax'] ? 'gutengeek-post-filter-ajax' : '' ); ?>" method="GET">
		<div class="gutengeek-post__filter-tab-items">
			<?php if ( isset($attributes['filterRoot']) && $attributes['filterRoot'] ) : ?>
				<a
					href="#"
					class="gutengeek-post__filter-tab-item gutengeek-post__filter-all <?php echo ( isset( $_GET['term'] ) && $_GET['term'] === '' ) || ! isset( $_GET['term'] ) ? 'gutengeek-active' : '';
					?>"
					title="<?php echo esc_attr( $attributes['filterRootText'] ); ?>"
					data-slug=""
					data-name="<?php echo esc_attr( $attributes['filterRootText'] ); ?>">
					<?php echo esc_html( ! empty( $attributes['filterRootText'] ) ? $attributes['filterRootText'] : '' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( isset( $attributes['termType'] ) && $attributes['termType'] ) : ?>
				<?php foreach ( $attributes['termType'] as $term_slug ) : ?>
					<?php
					$term = get_term_by( 'slug', $term_slug, $attributes['taxonomyType'] );
					if ( ! $term ) {
						continue;
					}

					$item_classes = [
						'gutengeek-post__filter-tab-item',
						$attributes['taxonomyType'] ? 'gutengeek-post-filter-' . $attributes['taxonomyType'] . '-' . $term->slug : '',
						isset( $_GET['term'] ) && $_GET['term'] === $term->slug ? 'gutengeek-active' : '',
					];
					?>
					<a
						href="#"
						class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $item_classes ) ) ); ?>"
						title="<?php echo esc_attr( $term->name ); ?>"
						data-taxonomy="<?php echo esc_attr( $attributes['taxonomyType'] ); ?>"
						data-slug="<?php echo esc_attr( $term->slug ); ?>"
						data-name="<?php echo esc_attr( $term->name ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<input type="hidden" name="term" value="<?php echo isset( $_GET['term'] ) && $_GET['term'] ? esc_attr( $_GET['term'] ) : '' ?>">
	</form>
</div>
