<?php

namespace Gtg_Advanced_Blocks\Admin;

class Settings_Page {

	public function __construct() {
		add_action( 'gutengeek_setting_page_content', [ __CLASS__, 'setting_page_content' ] );
	}

	public static function update_settings() {
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( !empty( $_REQUEST['page'] ) && GTG_AB_SLUG == $_REQUEST['page'] ) {

		}
	}

	/**
	 * generate settings page
	 */
	public static function render_settings_page() {
		include_once GTG_AB_DIR . 'views/admin/admin.php';
	}

	/**
	 * Renders the admin settings page content.
	 */
	public static function setting_page_content() {
		$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		$action = ( !empty( $action ) && '' != $action ) ? $action : 'settings';
		$action = str_replace( '_', '-', $action );
		include_once GTG_AB_DIR . 'views/admin/' . $action . '.php';
	}

}

new Settings_Page();
