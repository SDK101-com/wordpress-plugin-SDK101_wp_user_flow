<?php
/**
* Plugin Name: sdk101 WordPress User Flow - Login/Registration/Forget_Password
* Version: 1.0.1
* Description: sdk101 WordPress User Flow is a plugin that helps personalize Login/Registration/Forget_Password form.
* Author: SDK101
* Author URI: SDK101.com
* Tested up to: 
* License: GPLv3 or later
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 3, as
* published by the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!defined('SDK101_USER_FLOW_PLUGIN_PATH'))
{
	define('SDK101_USER_FLOW_PLUGIN_PATH', plugin_dir_path(__FILE__));
	define('PAGE_SLUG_LOGIN', 'login' );
	define('PAGE_SLUG_REGISTRATION', 'register' );
	define('PAGE_SLUG_PASSWORD_RESET', 'password-reset' );
	define('PAGE_SLUG_PASSWORD_LOST', 'password-lost' );
}

require_once (SDK101_USER_FLOW_PLUGIN_PATH . 'lib/class-plugin_activation.php');
require_once (SDK101_USER_FLOW_PLUGIN_PATH . 'lib/settings/class-settings_google_captcha.php');
require_once (SDK101_USER_FLOW_PLUGIN_PATH . 'lib/class-personalize_login.php');
require_once (SDK101_USER_FLOW_PLUGIN_PATH . 'lib/class-personalize_registration.php');
require_once (SDK101_USER_FLOW_PLUGIN_PATH . 'lib/class-personalize_password_reset.php');

// Initialize the plugin
$plugin_activation = new Plugin_Activation();
$settings_google_captcha = new Setting_Google_Captcha();
$personalize_login_pages = new Personalize_Login();
$personalize_registration_pages = new Personalize_Registration();
$personalize_password_reset_pages = new Personalize_Password_Reset();

/*
 *	Get CMB2. If using the plugin from wordpress.org, REMOVE THIS!
 */
if ( file_exists( dirname( __FILE__ ) . '/assets/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/assets/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/assets/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/assets/CMB2/init.php';
}

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'Plugin_Activation', 'plugin_activated' ) );


/*
 * The action callback function to store User Registration fields in UserMeta table, which otherwise cannot be saved with wp standard function wp_insert_user()
 * 
 * do_action( 'sdk101_registration_form_fields_to_usermeta_action', $result );
 * Called from Class "Personalize_Registration" under function "do_register_user()"
 * 
 */
function sdk101_registration_form_fields_to_usermeta( $newUserId ) {
	// (maybe) do something with the $newUserId.
    
}
add_action( 'sdk101_registration_form_fields_to_usermeta_action', 'sdk101_registration_form_fields_to_usermeta', 10, 1 );

/**
 * Enqueue Bootstrap.
 */
add_action('admin_enqueue_scripts', 'sdk101_user_flow_load_bootstrap');
add_action( 'wp_enqueue_scripts', 'sdk101_user_flow_load_bootstrap' );
function sdk101_user_flow_load_bootstrap() {
	
    wp_enqueue_style( 'sdk101-wp-user-flow-bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', false, '5.0.2');
	wp_enqueue_style('sdk101-wp-user-flow-bootstrap-css');

    wp_enqueue_script( 'sdk101-wp-user-flow-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array(  ), '5.0.2', true );
}

			
/**
 * Enqueue FontAwesome 4.7.
 */
add_action( 'wp_enqueue_scripts', 'sdk101_user_flow_load_fontawesome' );
add_action('admin_enqueue_scripts', 'sdk101_user_flow_load_fontawesome');
function sdk101_user_flow_load_fontawesome() {
	
    wp_enqueue_style( 'sdk101-wp-user-flow-fontawesom-css', 'https://use.fontawesome.com/releases/v5.7.1/css/all.css', false, '5.7.1' );
	wp_enqueue_style('sdk101-wp-user-flow-fontawesom-css');
}

/*
add_action( 'wp_enqueue_scripts', 'sdk101_wp_user_flow_register_styles' ); 
function sdk101_wp_user_flow_register_styles(){
	
	wp_enqueue_style( 'sdk101-wp-user-flow-fontAwesomeFree', 
						'https://kit.fontawesome.com/da71e7b887.js',
						 array(), '1.0' );
	
	
	}//End-of: function sdk101_wp_user_flow_register_styles()
*/