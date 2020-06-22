<div class="wrap">
	<div id="gutengeek-admin-about-page">
		<div class="gutengeek-admin-card gutengeek-admin-card-header gutengeek-mb-20">
			<div class="gutengeek-header-left">
				<h3 class="gutengeek-admin-card-title">
					<?php printf(
						wp_kses(
							/* translators: %s: VERSION. Only visible to screen readers. */
							__( 'GutenGeek Advanced Blocks&nbsp;-&nbsp;%s', 'gutengeek' ),
							array(
								'span' => [ 'class' => [] ],
							)
						),
						GTG_AB_VER
					); ?>
				</h3>
				<h4 class="gutengeek-options-section-subtitle"><?php esc_attr_e( 'Powerful Gutenburg Toolkit', 'gutengeek' ) ?></h4>
				<div class="gutengeek-card-header-button-group">
					<a href="<?php echo esc_attr( admin_url( 'admin.php?page=gutengeek' ) ) ?>" class="gutengeek-admin-button primary large"><?php _e( 'Go To Settings', 'gutengeek' ) ?></a>
					<a href="<?php echo esc_attr( GTG_AB_DOCUMENT_URL ) ?>" class="gutengeek-admin-button outline large">
						<?php _e( 'Documentation', 'gutengeek' ) ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
			</div>
			<div class="gutengeek-header-right gutengeek-option-logo">
				<img src="<?php echo esc_attr( GTG_AB_URL . 'assets/images/logo-gradient.svg' ) ?>" alt="<?php esc_attr_e( 'GutenGeek Logo', 'gutengeek' ); ?>" />
			</div>
		</div>

		<div class="gutengeek-about-container">
			<div class="gutengeek-about-content">
				<div class="gutengeek-admin-card features">
					<h4 class="gutengeek-admin-card-title is-medium is-sub">
						<?php _e( 'GutenGeek Core Features', 'gutengeek' ) ?>
					</h4>
					<ul class="gutengeek-options-features">
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Global page settings(typography, color, ...)', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Predefined sections', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Pack UI Kit', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Highly customizable row columns', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Row full backgrounds style', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Drag column resizing', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Shape divider', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Device specific responsive controls', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Unlimited Google fonts', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Classic & gradient color and background', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Built-in animation', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Box-shadow', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Border', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Advanced Typography', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Font Awesome 5 icon picker', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Divider flexiable', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Templates Library', 'gutengeek' ); ?></li>
						<li><span class="dashicons dashicons-yes"></span> <?php esc_attr_e( 'Custom CSS', 'gutengeek' ); ?></li>
					</ul>
					<div class="gutengeek-mb-30">
						<a href="<?php echo esc_attr( GTG_AB_HOME_URL ) ?>" class="gutengeek-admin-button outline large">
							<?php _e( 'Read More', 'gutengeek' ) ?>
							<span class="dashicons dashicons-arrow-right-alt"></span>
						</a>
					</div>
				</div>
			</div>
			<div class="gutengeek-about-side">
				<div class="gutengeek-admin-card member">
					<h4 class="gutengeek-admin-card-title is-small">
						<?php _e( 'Empower Your Website with all functionalities & flexibility of Gutengeek', 'gutengeek' ) ?>
					</h4>
					<a href="https://gutengeek.com/my-account/" class="gutengeek-admin-button large gutengeek-block is-default">
						<?php _e( 'Become Member', 'gutengeek' ) ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
				<div class="gutengeek-admin-card">
					<h4 class="gutengeek-admin-card-title is-small">
						<?php _e( 'Still have question? Ask Us, now!', 'gutengeek' ) ?>
					</h4>
					<p><?php _e( 'We are always ready with all fix issues you stuck to ensure smoothly works with Gutengeek', 'gutengeek' ) ?></p>
					<a href="https://gutengeek.com/contact/" class="gutengeek-admin-button primary large gutengeek-block"><?php _e( 'Get Support', 'gutengeek' ) ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
