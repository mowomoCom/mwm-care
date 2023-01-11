<?php

if ( ! function_exists( 'mwmcp_redirect_web' ) ) {
	/**
	 * Redirect to maintenance page
	 */
	function mwmcp_redirect_web() {
		if( strcmp( get_option( '_main_acti' ), 'active' )  === 0 ){
			if( ( isset( $_GET['tk'] ) && strcmp( $_GET['tk'], get_option( '_main_acce_toke', '' ) ) === 0 ) || ( isset( $_COOKIE['mwmpc_cookie_token'] ) && strcmp( $_COOKIE['mwmpc_cookie_token'], get_option( '_main_acce_toke', '' ) ) === 0 ) ){
				setcookie( 'mwmpc_cookie_token', get_option( '_main_acce_toke', '' ), time() + ( 24 * 60 * 60 ) );
				add_action( 'wp_footer', 'mwmpc_maintenance_bar' );
			} else{
				global $pagenow;
				define('IN_MAINTENANCE', true);
				if( defined( 'IN_MAINTENANCE' ) && IN_MAINTENANCE && $pagenow !== 'wp-login.php' && ! is_user_logged_in() ) {
					if ( file_exists( mwmpc_TPL . 'public/maintenance.php' ) ) {
						require_once( mwmpc_TPL . 'public/maintenance.php' );
					}
					die();
				}
			}
		}
	};
	add_action( 'wp_loaded', 'mwmcp_redirect_web' );
}

if ( ! function_exists( 'mwmpc_maintenance_bar' ) ) {
	function mwmpc_maintenance_bar() {
		?>
			<div id="mantenimiento"> 
				<p><?php _e( 'Este sitio web está cerrado al publico, está viendo la versión en', 'mwmcp' ); ?><span> <?php _e( 'desarrollo', 'mwmcp' ); ?></span>.</p>
			</div>
			<style>
			#mantenimiento {
				position:fixed;
				font-family:verdana,arial;
				font-size:11pt;
				text-align:center;
				top: 0px;
				left: 0px;
				width:100%;
				background-color:#FCAF62; 
				z-index: 9999;
			}
			#mantenimiento p {
				font-family: verdana,arial;
				font-size: 10pt;
				color: #fff;
			}
			.site-header {
				top: 49px;
			}
				html {
				padding-top: 50px;
			}
			</style>
		<?php
	}
}
