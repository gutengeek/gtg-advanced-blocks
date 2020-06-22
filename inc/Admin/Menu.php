<?php

namespace Gtg_Advanced_Blocks\Admin;

class Menu {

	public static function init() {
		// register admin menu
		add_action( 'network_admin_menu', [ __CLASS__, 'register_menu' ] );
		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
		add_action( 'admin_menu', [ __CLASS__, 'admin_menu_change_name' ], 100 );
		add_action( 'admin_init', [ __CLASS__, 'redirect_external_handler' ] );
	}

	/**
	 * Register admin menu settings
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function register_menu() {

		$menu_icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24">
<g>
	<path fill="#0060f3" d="m21.916722,5.473895l-10.559607,4.415564l2.015925,4.415564l5.08781,-2.115791c0,2.115791 -1.151957,3.679637 -3.551868,4.691537c-1.919928,0.827918 -3.551868,0.919909 -5.08781,0.275973c-1.535943,-0.643936 -2.6879,-1.747828 -3.359875,-3.311673c-0.767971,-1.563846 -0.767971,-3.127691 -0.191993,-4.599546c0.575979,-1.471855 1.727936,-2.575746 3.359875,-3.219682c1.055961,-0.459955 2.015925,-0.643936 2.975889,-0.551946c0.959964,0 1.823932,0.275973 2.495907,0.735927l3.359875,-4.415564c-1.535943,-1.0119 -3.359875,-1.563846 -5.3758,-1.747828c-2.015925,-0.183982 -3.935853,0.183982 -5.951778,1.0119c-3.071886,1.287873 -5.183807,3.403664 -6.335764,6.347374c-1.151957,2.94371 -1.055961,5.795428 0.287989,8.739138c1.34395,2.94371 3.551868,4.96751 6.527757,6.071401c2.975889,1.103891 6.143771,1.0119 9.407649,-0.367964c3.071886,-1.287873 5.08781,-3.219682 6.239767,-5.887419c1.055961,-2.667737 0.959964,-5.427465 -0.383986,-8.371174l-0.959964,-2.115791z"/>
</g>
</svg>' );
		$root_menu = add_menu_page( GTG_AB_PLUGIN_NAME,
			GTG_AB_PLUGIN_NAME,
			apply_filters( 'gutengeek_admin_root_menu_capability', 'manage_options' ),
			GTG_AB_SLUG,
			[ '\Gtg_Advanced_Blocks\Admin\Settings_Page', 'render_settings_page' ],
			$menu_icon,
			3
		);

		// about page
		add_submenu_page(
			GTG_AB_SLUG,
			__( 'About GutenGeek', 'gutengeek' ),
			__( 'About', 'gutengeek' ),
			'manage_options',
			'gutengeek-about',
			[ __CLASS__, 'render_about_page' ]
		);

		// to go pro
		add_submenu_page(
			GTG_AB_SLUG,
			'',
			'<span class="dashicons dashicons-star-filled" style="font-size: 17px"></span> ' . __( 'Upgrade', 'gutengeek' ),
			'manage_options',
			'gutengeek-upgrade',
			[ __CLASS__, 'redirect_external_handler' ],
			99
		);

		do_action( 'gutengeek_registered_menu', $root_menu, GTG_AB_SLUG );
	}

	/**
	 * render about page
	 */
	public static function render_about_page() {
		include GTG_AB_ROOT . '/views/admin/about.php';
	}

	/**
	 * redirect external url
	 */
	public static function redirect_external_handler() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}
		global $pagenow;
		if ( $_GET['page'] == 'gutengeek' && ! current_user_can( 'manage_options' )  ) {
			wp_die( __('You don’t have permission to access this page', 'gutengeek' ) );
		}


		if ( $_GET['page'] == 'gutengeek-upgrade' ) {
			wp_redirect( GTG_AB_GO_TO_PRO_URL );
			exit();
		}
	}

	/**
	 * Rename 'Gtg_Advanced_Blocks\' submenu => 'Settings'
	 */
	public static function admin_menu_change_name() {
		global $submenu;

		if ( isset( $submenu[ GTG_AB_SLUG ] ) ) {
			$submenu[ GTG_AB_SLUG ][0][0] = __( 'Settings', 'gutengeek' );
		}
	}

}
