<?php

if (!defined('ABSPATH')) exit;

if ( !function_exists( 'mwmpc_admin_menu' ) ) {
	function mwmpc_admin_menu() {
		add_submenu_page( MWM_FRA_SLUG, __('Care', 'mwmpc' ), __('Care', 'mwmpc' ), 'manage_options', mwmpc_SLUG, 'mwmpc_admin_menu_callback' );
	}
	add_action( 'admin_menu', 'mwmpc_admin_menu', 90 );
}

if ( !function_exists( 'mwmpc_admin_menu_callback' ) ) {
	function mwmpc_admin_menu_callback() {
		$args = array();
		load_template( mwmpc_TPL . 'admin/general/general.php', false, $args );
	}
}