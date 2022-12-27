<?php
/*
 * Plugin Name: mowomo Care
 * Description: Plugin de mowomo Care
 * Version:     0.5.1
 * Author:      mowomo
 * Author URI:  https://www.mowomo.com
 * License:     GNU GPL 3
 * Text Domain: mwmpc
*/

// Constants
if ( !defined( 'ABSPATH' ) )    exit;
if ( !defined( 'mwmpc_NAME' ) )  define( 'mwmpc_NAME', 'mowomo Care' );
if ( !defined( 'mwmpc_SLUG' ) )  define( 'mwmpc_SLUG', 'mwmpc' );
if ( !defined( 'mwmpc_VER' ) )   define( 'mwmpc_VER', '0.5.1' );
if ( !defined( 'mwmpc_FILE' ) )  define( 'mwmpc_FILE', __FILE__ );
if ( !defined( 'mwmpc_URL' ) )   define( 'mwmpc_URL', plugins_url('/', mwmpc_FILE) );
if ( !defined( 'mwmpc_JS' ) )    define( 'mwmpc_JS', mwmpc_URL . 'assets/js/' );
if ( !defined( 'mwmpc_CSS' ) )   define( 'mwmpc_CSS', mwmpc_URL . 'assets/css/' );
if ( !defined( 'mwmpc_IMG' ) )   define( 'mwmpc_IMG', mwmpc_URL . 'assets/images/' );
if ( !defined( 'mwmpc_DIR' ) )   define( 'mwmpc_DIR', plugin_dir_path( mwmpc_FILE ) );
if ( !defined( 'mwmpc_INC' ) )   define( 'mwmpc_INC', mwmpc_DIR . 'includes/' );
if ( !defined( 'mwmpc_TPL' ) )   define( 'mwmpc_TPL', mwmpc_DIR . 'templates/' );
if ( !defined( 'mwmpc_PRO' ) )   define( 'mwmpc_PRO', TRUE );
if (!defined('mwmpc_FRA')) {
    define('mwmpc_FRA', mwmpc_DIR.'mwm-dash/');
}

// Included files
include_once mwmpc_INC.'functions.php';

if ( !wp_script_is( 'jquery', 'enqueued' ) ) {
    wp_enqueue_script( 'jquery' );
}

/**
 * Check if exists the function 'mwmpc_constructor'.
 *
 * @since 1.3.0
 */
if (!function_exists('mwmpc_constructor')) {
    /**
     * Plugin Construction.
     * 
     * Function that builds the complete plugin structure.
     *
     * @since 1.3.0
     * 
     * @global string mwmpc_FRA Shortcut to folder includes
     */
    function mwmpc_constructor()
    {
        /**
         * Check if exists the function 'mwm_dashboard_constructor'.
         *
         * @since 1.3.0
         */
        if (!function_exists('mwm_dashboard_constructor')) {
            require_once mwmpc_FRA.'index.php';
            mwm_dashboard_constructor();
        }

		mwm_dashboard()->add_plugin( new mwm_plugin( array(
			'slug' 				=> mwmpc_SLUG,
			'name' 				=> 'mowomo Care',
			'version' 			=> mwmpc_VER,
			'pro' 				=> mwmpc_PRO,
			'update_message'	=> 'mensaje de actualización',
			'file'				=> mwmpc_FILE,
		)));
	}
    add_action('plugins_loaded', 'mwmpc_constructor');
}

// Enqueue assets
if (!function_exists('mwmpc_enqueue_scripts')) {
    function mwmpc_enqueue_scripts() {
        wp_register_script( mwmpc_SLUG.'_scripts', mwmpc_JS.'scripts.js', array('jquery'), mwmpc_VER, true );
        wp_register_style( mwmpc_SLUG.'_styles', mwmpc_CSS.'styles.css', array(), mwmpc_VER );
        wp_enqueue_script( mwmpc_SLUG.'_scripts' );
        wp_enqueue_style( mwmpc_SLUG.'_styles' );
    }
    add_action('wp_enqueue_scripts', 'mwmpc_enqueue_scripts', 999);
}

// Enqueue admin assets
if (!function_exists('mwmpc_enqueue_admin_scripts')) {
    function mwmpc_enqueue_admin_scripts() {
        wp_register_script( mwmpc_SLUG.'_admin_scripts', mwmpc_JS.'admin_scripts.js', array('jquery'), mwmpc_VER, true );
		wp_localize_script( mwmpc_SLUG.'_admin_scripts', 'mwmpc_vars', array(
			'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
			'error_message'				=> __( 'An unexpected error has occurred, try again later', 'mwmpc' ),
			'regenerating_text_loading'	=> __( 'Regenerating...', 'mwmpc' ),
			'regenerating_text'			=> __( 'Regenerate access url', 'mwmpc' ),
			'copy_text'					=> __( 'Click to copy', 'mwmpc' ),
			'copied_text'				=> __( '¡Copied!', 'mwmpc' ),
		) );
        wp_register_style( mwmpc_SLUG.'_admin_styles', mwmpc_CSS.'admin_styles.css', array(), mwmpc_VER );
        wp_register_style( mwmpc_SLUG.'_admin_styles_font', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css', array(), mwmpc_VER );
        wp_enqueue_script( mwmpc_SLUG.'_admin_scripts' );
        wp_enqueue_style( mwmpc_SLUG.'_admin_styles' );
        wp_enqueue_style( mwmpc_SLUG.'_admin_styles_font' );
    }
    add_action('admin_enqueue_scripts', 'mwmpc_enqueue_admin_scripts', 999);
}

// Adding textdomain
if (!function_exists('mwmpc_load_textdomain')) {
    function mwmpc_load_textdomain() {
        load_plugin_textdomain( mwmpc_SLUG, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    add_action( 'plugins_loaded', 'mwmpc_load_textdomain' );
}


/* TODO: 
- Añadir notificación con icono cuando no tiene mantenimiento activo de care.
- Añadir notificación cuando cambie el contenido del sumario.
- Añadir caracteristicas del plan de hosting.
- Añadir si tiene hosting los datos de uso (Espacio ocupado de ftp, correo electrónico y base de datos).
- Añadir aviso si se sobrepasan los limites uso de hosting.
*/