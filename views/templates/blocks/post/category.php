<?php $categories = wp_get_post_terms( get_the_ID(), 'category', array( 'fields' => 'all' ) ); ?>
<?php if ( $categories && count($categories) > 0 ) : ?>
	<span class="entry-meta entry-category">
		<?php the_category( ', ' ) ?>
	</span>
<?php endif; ?>
