<?php

// Security
if (!defined('ABSPATH')) exit;

// Include files
include_once mwmpc_INC.'admin/functions.php';
include_once mwmpc_INC.'public/functions.php';
include_once mwmpc_INC.'schema/functions.php';

if ( !function_exists( 'echop' ) ) {
    /**
	 * Function that shows all the content of a variable
	 */
    function echop( $var ) {
        echo '<pre>', var_dump( $var ), '</pre>';
    }
}

if ( ! function_exists( 'echopa' ) ) {
	/**
	 * Function that shows all the content of a variable only to editors and admins
	 */
	function echopa( $var ) {
		if( current_user_can('editor') || current_user_can('administrator') ) {
			echop( $var );
		}
	}
}
