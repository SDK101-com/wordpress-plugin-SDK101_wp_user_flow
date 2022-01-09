<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811

class Personalize_Password_Reset {
 	private $isGoogleCaptchActive;
	public $setings;
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
		
		$this->assignSetingsVariable();
	
	//Redirects the user to the custom "Forgot your password?" page instead of wp-login.php?action=lostpassword.
	add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );
	
	//A shortcode for rendering the form used to initiate the password reset.
	add_shortcode( 'custom-password-lost-form', array( $this, 'render_password_lost_form' ) );
	
	//Initiates password reset.
	add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );
	
	//Returns the message body for the password reset mail.
	add_filter( 'retrieve_password_message', array( $this, 'replace_retrieve_password_message' ), 10, 4 );
	//Returns the title for the password reset mail.
	add_filter( 'retrieve_password_title', array( $this, 'replace_retrieve_password_title' ), 10, 3 );
	
	
	//Redirects to the custom password reset page, or the login page if there are errors.
	add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
	add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );
	
	//A shortcode for rendering the form used to reset a user's password
	add_shortcode( 'custom-password-reset-form', array( $this, 'render_password_reset_form' ) );
	
	//
	add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
	add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );
	
    }//End-of: function __construct()
    

	/*
	 *
	 */
	public function assignSetingsVariable(){
		$this->isGoogleCaptchActive = (is_array(get_option( 'personalize-login-recaptcha-activate' ) )? true:false);
		
		/*
		    * Reaf data from SchoolPress School Settings Page
			*/
			global $wpdb;
			$wpsp_settings_table    =   $wpdb->prefix."wpsp_settings";
            $wpsp_settings_edit     =   $wpdb->get_results("SELECT * FROM $wpsp_settings_table" );
			
			$schoool_settings_data = array();
			foreach( $wpsp_settings_edit as $sdat ) {
            	$schoool_settings_data[$sdat->option_name]  =   sanitize_text_field( $sdat->option_value );
            }
			
			
		  $this->setings  = array(
				'email_notify' => 'both',
				'From-email'	=> 'sdk101.mailbox@gmail.com',
				'ReplyTo-email'	=> 'sdk101.mailbox@gmail.com',
				//'logoURL'	=>	isset( $schoool_settings_data['sch_logo'] ) ? $schoool_settings_data['sch_logo'] : null,
				'blogname'	=>	isset( $schoool_settings_data['sch_name'] ) ? sanitize_text_field( $schoool_settings_data['sch_name']) : null,
			);
	}
		   
	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
		$user = wp_get_current_user();
		if ( user_can( $user, 'manage_options' ) ) {
			if ( $redirect_to ) {
				wp_safe_redirect( $redirect_to );
			} else {
				wp_redirect( admin_url() );
			}
		} else {
			wp_redirect( home_url() );
		}
	}//End-of: function redirect_logged_in_user
	
	
	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 */
	public function redirect_to_custom_lostpassword() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}
	
			wp_redirect( home_url(PAGE_SLUG_PASSWORD_LOST ) ); //password-lost
			exit;
		}
	}//End-of: fuction redirect_to_custom_lostpassword()
	
	/**
	 * A shortcode for rendering the form used to initiate the password reset.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_lost_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		
		// Check if the user just requested a new password 
		$attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
		
		// Retrieve possible errors from request parameters
		$attributes['errors'] = array();
		if ( isset( $_REQUEST['errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['errors'] );
		
			foreach ( $error_codes as $error_code ) {
				$attributes['errors'] []= $this->get_error_message( $error_code );
			}
		}
		
		// Check whether to show Google Captcha or not
		if($this->isGoogleCaptchActive){
			$attributes['recaptcha_site_key'] = sanitize_text_field( get_option( 'personalize-login-recaptcha-site-key', null ) );
		}
		
		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'personalize-login' );
		} else {
			return $this->get_template_html( 'password_lost_form', $attributes );
		}
	}//End-of: function render_password_lost_form()
	
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
	 * Initiates password reset. Executes After the Password_Lost form is submitted and checks if the details entered by user are valid or not.
	 */
	public function do_password_lost() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			
			// Perform nonce check for Password Lost Form
			$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
			if( wp_verify_nonce($nonce , 'sdk101-user-flow-password-lost-form') != 1 ){
				// Recaptcha check failed, display error
				$redirect_url = home_url( PAGE_SLUG_PASSWORD_LOST );
				$redirect_url = add_query_arg( 'errors', 'suspicious', $redirect_url );
					
				wp_redirect( $redirect_url );
				exit;				
			}//End-of: if( wp_verify_nonce($nonce 
			
			// Perform Google Captcha check if Captcha details are entered
			if( $this->isGoogleCaptchActive ){ 
				if ( ! $this->verify_recaptcha() ) {
					// Recaptcha check failed, display error
					$redirect_url = home_url( PAGE_SLUG_PASSWORD_LOST );
					$redirect_url = add_query_arg( 'errors', 'captcha', $redirect_url );
					
					wp_redirect( $redirect_url );
					exit;
				}
			}//End-of: if($this->isGoogleCaptchActive )
			
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = home_url( PAGE_SLUG_PASSWORD_LOST );
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = home_url( PAGE_SLUG_LOGIN);//'member-login' );
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
			}
	
			wp_redirect( $redirect_url );
			exit;
		}
	}//End-of: function do_password_lost() 
	
	
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
			
			case 'invalid_email':
			case 'invalidcombo':
				return get_option( 'personalize-error_message_invalid_username' )?get_option( 'personalize-error_message_invalid_username' ):__("We don't have any users with that email address. Maybe you used a different one when signing up?",
					'personalize-login'
				);
	 		case 'captcha':
				return get_option( 'personalize-error_message_captcha' )?get_option( 'personalize-error_message_captcha' ):__( 'The Google reCAPTCHA check failed.', 'personalize-login' );
			case 'suspicious':
				return get_option( 'personalize-error_message_suspicious' )?get_option( 'personalize-error_message_suspicious' ):__( "Sorry, your login seems suspicious.", 'personalize-login' );
			default:
				break;
		}
		 
		return __( 'An unknown error occurred. Please try again later.', 'personalize-login' );
	}//End-of: function get_error_message()
	
	
	/**
	 * Returns the message body for the password reset mail.
	 * Called through the retrieve_password_message filter.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 *
	 * @return string   The mail message to send.
	 */
	public function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		// Create new message
		$msg  = __( 'Hello!', 'personalize-login' ) . "\r\n\r\n";
		$msg .= sprintf( __( 'You asked us to reset your password for your account using the email address %s.', 'personalize-login' ), $user_login ) . "\r\n\r\n";
		$msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'personalize-login' ) . "\r\n\r\n";
		$msg .= __( 'To reset your password, visit the following address:', 'personalize-login' ) . "\r\n\r\n";
		$msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
		$msg .= __( 'Thanks!', 'personalize-login' ) . "\r\n";
	
		return $msg;
	}//End-of: function replace_retrieve_password_message()
	
	/**
     * Filters the subject of the password reset email.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
	 *
	 * @param string  $title      Email subject.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 */
	public function replace_retrieve_password_title( $title, $user_login, $user_data ) {
		// Create new message
		$title = !empty( $this->setings['blogname'] ) ? ($this->setings['blogname']." Password Reset") : $title;
	
		return $title;
	}//End-of: function replace_retrieve_password_message()
	
	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 */
	public function redirect_to_custom_password_reset() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( sanitize_text_field( $_REQUEST['key'] ), sanitize_text_field( $_REQUEST['login'] ) );
			
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( PAGE_SLUG_LOGIN.'?login=expiredkey') );//'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( PAGE_SLUG_LOGIN.'member-login?login=invalidkey'));//'member-login?login=invalidkey' ) );
				}
				exit;
			}
	
			$redirect_url = home_url( PAGE_SLUG_PASSWORD_RESET );
			$redirect_url = add_query_arg( 'login', esc_attr( sanitize_text_field( $_REQUEST['login'] ) ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( sanitize_text_field( $_REQUEST['key'] ) ), $redirect_url );
	
			wp_redirect( $redirect_url );
			exit;
		}
	}//End-of: function redirect_to_custom_password_reset()
	
	
	
	/**
	 * A shortcode for rendering the form used to reset a user's password.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_reset_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
	
		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'personalize-login' );
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login']	= sanitize_text_field( $_REQUEST['login'] );
				$attributes['key']		= sanitize_text_field( $_REQUEST['key'] );
	
				// Error messages
				$errors = array();
				if ( isset( $_REQUEST['error'] ) ) {
					$error_codes = explode( ',', $_REQUEST['error'] );
	
					foreach ( $error_codes as $code ) {
						$errors []= $this->get_error_message( $code );
					}
				}
				$attributes['errors'] = $errors;
	
				return $this->get_template_html( 'password_reset_form', $attributes );
			} else {
				return __( 'Invalid password reset link.', 'personalize-login' );
			}
		}
	}//End-of: A shortcode for rendering the form used to reset a user's password
	
	
	
	/**
	 * Resets the user's password based on Password and Confirme-Password entered, if the password reset form was submitted.
	 */
	public function do_password_reset() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$rp_key = sanitize_text_field( $_REQUEST['rp_key'] );
			$rp_login = sanitize_text_field( $_REQUEST['rp_login'] );
	
			$user = check_password_reset_key( $rp_key, $rp_login );
	
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( PAGE_SLUG_LOGIN.'?login=expiredkey'));//'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( PAGE_SLUG_LOGIN.'?login=invalidkey'));//'member-login?login=invalidkey' ) );
				}
				exit;
			}
	
			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = home_url( PAGE_SLUG_PASSWORD_RESET );
	
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
	
					wp_redirect( $redirect_url );
					exit;
				}
	
				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = home_url( PAGE_SLUG_PASSWORD_RESET );
	
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
	
					wp_redirect( $redirect_url );
					exit;
				}
	
				// Parameter checks OK, reset password
				reset_password( $user, sanitize_text_field( $_POST['pass1'] ) );
				wp_redirect( home_url( PAGE_SLUG_LOGIN.'?password=changed'));//'member-login?password=changed' ) );
			} else {
				echo "Invalid request.";
			}
	
			exit;
		}
	}//End-of: function do_password_reset()
	
	
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
}//End-of: class Personalize_Password_Reset
?>