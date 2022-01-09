<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627

class Personalize_Login {
	
	private $isGoogleCaptchActive;
 
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
		//Set initial values.
		$this->assignSetingsVariable();
		
		//Shortcode for rendering the login form. This function is called when login-page is loded.
		add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );
		
		//Redirect the user to the custom login page instead of wp-login.php.
		add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
		
		//Redirect the user after authentication if there were any errors.
		add_filter( 'authenticate', array( $this, 'maybe_redirect_at_authenticate' ), 101, 3 );
		
		//Returns the URL to which the user should be redirected after the (successful) login.
		add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );
		
		//Redirect to custom login page after the user has been logged out.
		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
		
		//Determine if user is allowed to access the Dashboard.
		add_action( 'init', array( $this, 'restrict_access_to_wp_dashboard' ) );
		
		//Filters content to display in the middle of the login form.
		//https://developer.wordpress.org/reference/functions/wp_login_form/
		//add_filter('login_form_bottom', array( $this, 'add_fields_at_endof_login_form'), 10, 2 );
		add_filter('login_form_middle', array( $this, 'add_fields_at_endof_login_form'), 10, 2 );
		
		//Redirect user if useer is logged-in and is trying to access login or registration page
		add_action('wp', array($this, 'redirect_logged_in_user') );
     
    }//End-of: function __construct()
    
	/*
	 *
	 */ 
	 private function assignSetingsVariable(){
		 
		 $this->isGoogleCaptchActive = (is_array(get_option( 'personalize-login-recaptcha-activate' ) )? true:false);
		 
		 }
	 
	 /**
	 * A shortcode for rendering the login form. This function is called when login-page is loded.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_login_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		$show_title = $attributes['show_title'];
	 
		if ( is_user_logged_in() ) {
			//For this scenario, user is automatically redirected to home page wit the help of function: redirect_logged_in_user()
			//return __( 'You are already signed in.', 'personalize-login' );
			//User will be redirected as per function redirect_logged_in_user , if user is already logged-in.
		}
		 
		// Pass the redirect parameter to the WordPress login functionality: by default,
		// don't specify a redirect, but if a valid redirect URL has been passed as
		// request parameter, use it.
		$attributes['redirect'] = '';
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] ); //wp_validate_redirect: Validates a URL for use in a redirect.
		}
		 
		 // Error messages
		$errors = array();
		if ( isset( $_REQUEST['login'] ) ) {
			$error_codes = explode( ',', $_REQUEST['login'] );
		 
			foreach ( $error_codes as $code ) {
				$errors []= $this->get_error_message( $code );
			}
		}
		$attributes['errors'] = $errors;
		
		// Check if the user just registered
		$attributes['registered'] = is_email( $_REQUEST['registered'])?$_REQUEST['registered']:'';
		if($attributes['registered']){
			$attributes['registered_email'] = $attributes['registered'];
		}
		
		// Check whether to show Google Captcha or not
		if($this->isGoogleCaptchActive){
			$attributes['recaptcha_site_key'] = sanitize_text_field(get_option( 'personalize-login-recaptcha-site-key', null ));
		}
		
		
		// Check if user just logged out
		$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
		
		// Check if user just updated password
		$attributes['password_updated'] = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';
		
		// If Password Reset link is sent successfully
		$attributes['checkemail'] = isset( $_REQUEST['checkemail'] )? $_REQUEST['checkemail'] : '';
		

		// Render the login form using an external template
		return $this->get_template_html( 'login_form', $attributes );
	}//End-of: function render_login_form()
	
	
	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 */
	private function get_template_html( $template_name, $attributes = null ) {
		if ( ! $attributes ) {
				$attributes = array();
		}
		
		ob_start();
			do_action( 'personalize_login_before_' . $template_name );
			require( SDK101_USER_FLOW_PLUGIN_PATH.'templates/' . $template_name . '.php');
			do_action( 'personalize_login_after_' . $template_name );
			
		$html = ob_get_contents();
		ob_end_clean();
	 	
		return $html;
	}//End-of: function get_template_html()
	
	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login() {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? wp_validate_redirect($_REQUEST['redirect_to']) : null;
		 
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user( $redirect_to );
				exit;
			}
	 
			// The rest are redirected to the login page
			$login_url = home_url( PAGE_SLUG_LOGIN );//login page URL
			if ( ! empty( $redirect_to ) ) {
				$login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
			}
	 
			wp_redirect( $login_url );
			exit;
		}
	}//End-of: function redirect_to_custom_login()
	
	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	public function redirect_logged_in_user( $redirect_to = null ) {
		$user = wp_get_current_user();
		
		if( is_user_logged_in() && ( is_page(PAGE_SLUG_LOGIN) || is_page(PAGE_SLUG_REGISTRATION) ) ){
			//Function to rediret Loggedin user to HOME page when user tried to access Login or Registration page.
			if($redirect_to){
				wp_redirect(home_url($redirect_to));
			}else{
				wp_redirect( home_url() );
			}
			exit;
		}//End-of: if( is_user_logged_in() )
		
	}//End-of: function redirect_logged_in_user
	
	
	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate( $user, $username, $password ) {
		// Check if the earlier authenticate filter (most likely, the default WordPress authentication) functions have found errors
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			
			$login_url = home_url( PAGE_SLUG_LOGIN );//'member-login'
			
			$nonce = sanitize_text_field($_REQUEST['_wpnonce']);
			if( wp_verify_nonce($nonce , 'sdk101-user-flow-login-form') != 1 ){ // Perform nonce check for Login Form
				// Recaptcha check failed, display error
				$login_url = add_query_arg( 'login', 'suspicious', $login_url );
				wp_redirect( $login_url );
				exit;
				
			}//End-of: if( wp_verify_nonce($nonce 
							
			if( $this->isGoogleCaptchActive ){ // Perform Google Captcha check if Captcha details are entered
				if ( ! $this->verify_recaptcha() ) {
					// Recaptcha check failed, display error
					$login_url = add_query_arg( 'login', 'captcha', $login_url );
					wp_redirect( $login_url );
					exit;
				}
			}//End-of: if($this->isGoogleCaptchActive )
						
			if ( is_wp_error( $user ) ) {
				$error_codes = join( ',', $user->get_error_codes() );
	 
				$login_url = add_query_arg( 'login', $error_codes, $login_url );
	 
				wp_redirect( $login_url );
				exit;
			}
		}
	 
		return $user;
	}//End-of: function maybe_redirect_at_authenticate
	
	
	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
		switch ( $error_code ) {
			
			case 'empty_username':
				return get_option( 'personalize-error_message_empty_username' )?get_option( 'personalize-error_message_empty_username' ):__( 'You do have an email address, right?', 'personalize-login' );
	 
			case 'empty_password':
				return get_option( 'personalize-error_message_empty_password' )?get_option( 'personalize-error_message_empty_password' ):__( 'You need to enter a password to login.', 'personalize-login' );
	 
			case 'invalid_username':
			case 'invalid_email':
				return get_option( 'personalize-error_message_invalid_username' )?get_option( 'personalize-error_message_invalid_username' ):__("We don't have any users with that email address. Maybe you used a different one when signing up?",
					'personalize-login'
				);
	 
			case 'incorrect_password':
				$err = get_option( 'personalize-error_message_incorrect_password' )?get_option( 'personalize-error_message_incorrect_password' ):__("The password you entered wasn't quite right. {{ForgetPasswordURL}}",
					'personalize-login'
				);
				$err1 = str_replace("{{ForgetPasswordURL}}","<a href='%s'>Did you forget your password</a>?",$err);
				return sprintf( $err1, wp_lostpassword_url() );

			// Reset password
			case 'expiredkey':
			case 'invalidkey':
				return get_option( 'personalize-error_message_invalidkey' )?get_option( 'personalize-error_message_invalidkey' ):__( 'The password reset link you used is not valid anymore.', 'personalize-login' );
			
			case 'password_reset_mismatch':
				return get_option( 'personalize-error_message_password_reset_mismatch' )?get_option( 'personalize-error_message_password_reset_mismatch' ):__( "The two passwords you entered don't match.", 'personalize-login' );
				
			case 'password_reset_empty':
				return get_option( 'personalize-error_message_password_reset_empty' )?get_option( 'personalize-error_message_password_reset_empty' ):__( "Sorry, we don't accept empty passwords.", 'personalize-login' );
			
			case 'captcha':
				return get_option( 'personalize-error_message_captcha' )?get_option( 'personalize-error_message_captcha' ):__( 'The Google reCAPTCHA check failed.', 'personalize-login' );
			case 'suspicious':
				return get_option( 'personalize-error_message_suspicious' )?get_option( 'personalize-error_message_suspicious' ):__( "Sorry, your login seems suspicious.", 'personalize-login' );
	 
			default:
				
		}
		 
		return __( 'An unknown error occurred. Please try again later.', 'personalize-login' );
	}//End-of: function get_error_message()
	
	
	/**
	 * Redirect to custom login page after the user has been logged out.
	 */
	public function redirect_after_logout() {
		$redirect_url = home_url( PAGE_SLUG_LOGIN.'?logged_out=true');//'member-login?logged_out=true' )
		wp_safe_redirect( $redirect_url );
		exit;
	}//End-of: function redirect_after_logout()
	
	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
		$redirect_url = home_url();
	 
		if ( ! isset( $user->ID ) ) {
			return $redirect_url;
		}
	 	
		// Use the redirect_to parameter if one is set, otherwise redirect to home_url()
		if ( $requested_redirect_to == '' ) {
			$redirect_url = home_url( );
		} else {
			$redirect_url = $requested_redirect_to;
		}
		
		return wp_validate_redirect( $redirect_url, home_url() );
	}//End-of: function redirect_after_login
	
	
	/**
	 * Determine if user is allowed to access the Dashboard.
	 *
	 * @return true if redirection is not required, which is the current user is granted access of WP Dashboard.
	 */
	public function restrict_access_to_wp_dashboard() {
		global $current_user;
		$current_user_role = $current_user->roles[0];
		
		$allowed_user_role = array('administrator');
		
		if ( is_admin() && ! in_array($current_user_role, $allowed_user_role) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
			wp_redirect( home_url() );
			exit; 
		}
		return true;
	}
	
	/**
     * Filters content to display in the middle of the login form.
     *
     * The filter evaluates just following the location where the 'login-password'
     * field is displayed.
     *
     * @since 3.0.0
     *
     * @param string $content Content to display. Default empty.
     * @param array  $args    Array of login form arguments.
     */
	 /*public function add_fields_at_endof_login_form($content, $args ){
		 
		 $custom_content = "";
		
		$recaptcha_site_key = sanitize_text_field(get_option( 'personalize-login-recaptcha-site-key', null ));
        if ( $recaptcha_site_key  ) :
		$custom_content .=	'<!-- Show Google Recaptcha -->
			<div class="recaptcha-container">
            	<div class="g-recaptcha" data-sitekey="'.$recaptcha_site_key.'"></div>
			</div>';
		endif;
		 
		 return $content. $custom_content;
	}//End-of: Function add_fields_at_endof_login_form()
	*/
	
	/**
	 * Checks that the reCAPTCHA parameter sent with the registration
	 * request is valid.
	 *
	 * @return bool True if the CAPTCHA is OK, otherwise false.
	 */
	private function verify_recaptcha() {
		// This field is set by the recaptcha widget if check is successful
		if ( isset ( $_POST['g-recaptcha-response'] ) ) {
			$captcha_response = sanitize_text_field( $_POST['g-recaptcha-response'] );
		} else {
			return false;
		}
	
		// Verify the captcha response from Google
		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret' => sanitize_text_field( get_option( 'personalize-login-recaptcha-secret-key' ) ),
					'response' => $captcha_response
				)
			)
		);
	
		$success = false;
		if ( $response && is_array( $response ) ) {
			$decoded_response = json_decode( $response['body'] );
			$success = $decoded_response->success;
		}
	
		return $success;
	}//End-of: function verify_recaptcha()
	
	
	/*
	 * Function to rediret Loggedin user to HOME page when user tried to access Login or Registration page.
	 *
	 */
	/*public function redirect_logged_in_user(){
		
		if ( is_user_logged_in() && (is_page(PAGE_SLUG_LOGIN) ||is_page(PAGE_SLUG_REGISTRATION) )) {
			wp_redirect(home_url());
			exit;
		}
	}*/

	
	
}//End-of: class Personalize_Login_Plugin
?>