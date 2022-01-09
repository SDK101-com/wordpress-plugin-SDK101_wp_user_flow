<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-2-new-user-registration--cms-23810

class Personalize_Registration {
	
	
	public $setings ;
	private $isGoogleCaptchActive;
 
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
		$this->assignSetingsVariable();
								
		//shortcode for rendering the new user registration form
		add_shortcode( 'custom-register-form', array( $this, 'render_register_form' ) );
		
		//Redirects the user to the custom registration page instead of wp-login.php?action=register.
		add_action( 'login_form_register', array( $this, 'redirect_to_custom_register') );
		
		//Handles the registration of a new user. And reads the fields from registration Form
		add_action( 'login_form_register', array( $this, 'do_register_user' ) );
		
		//An action function used to include the reCAPTCHA JavaScript file at the end of the page.
		add_action( 'wp_print_footer_scripts', array( $this, 'add_captcha_js_to_footer' ) );
		
		//Filter function to customize the User Registration Notification.
		add_filter( 'wp_new_user_notification_email', array($this, 'customized_wp_new_user_notification_email'), 10, 3 );
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
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_register_form( $attributes, $content = null ) {
		
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		
		// Retrieve possible errors from request parameters
		$attributes['errors'] = array();
		if ( isset( $_REQUEST['register-errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['register-errors'] );
		
			foreach ( $error_codes as $error_code ) {
				$attributes['errors'] []= $this->get_error_message( $error_code );
			}
		}
		
		// Check whether to show Google Captcha or not
		if($this->isGoogleCaptchActive){
			$attributes['recaptcha_site_key'] = sanitize_text_field( get_option( 'personalize-login-recaptcha-site-key', null ) );
		}
		
		if ( is_user_logged_in() ) {
			//For this scenario, user is automatically redirected to home page wit the help of function: redirect_logged_in_user()
			return __( 'You are already signed in.', 'personalize-login' );
		} elseif ( ! get_option( 'users_can_register' ) ) {
			return get_option( 'personalize-error_message_registration_closed' )?get_option( 'personalize-error_message_registration_closed' ):__( 'New user registering is currently not allowed.', 'personalize-login' );
		} else {
			return $this->get_template_html( 'register_form', $attributes );
		}
		
	} //End-of: function render_register_form()
	
	/*
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string	The contents of the template.
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
	 * Redirects the user to the custom registration page instead of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect( home_url( PAGE_SLUG_REGISTRATION ) ); //'member-register'
			}
			exit;
		}
	}//End-of: function redirect_to_custom_register()
	
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
				wp_redirect( home_url() );
				//wp_redirect( admin_url() );
			}
		} else {
			wp_redirect( home_url() );
		}
	}//End-of: function redirect_logged_in_user
	
	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param array $register_user	Stores details of new user received from Registration Form. Like email-address, First Name, Last name and other
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user( $register_user ) {
		
		$errors = new WP_Error();
	
		// Email address is used as both username and email. It is also the only
		// parameter we need to validate
		if ( ! is_email( $register_user['email'] ) ) {
			$errors->add( 'email', $this->get_error_message( 'email' ) );
			return $errors;
		}
	
		if ( username_exists( $register_user['email'] ) || email_exists( $register_user['email'] )) {
			$errors->add( 'email_exists', $this->get_error_message( 'email_exists') );
			return $errors;
		}
		
		/*
		 * Below fields can be fetched by creating settings page. This will give better control to site admin
		 */
		$show_admin_bar_front	= 'false'; // Accepts 'true' or 'false' as a string literal, not boolean. WP Admin top Bar will be inactive for all users
		$userRole 				= 'parent'; // Default Role for all registered users
		$password_length 		= (int)8; //The length of password to generate.Default is 12
		$password_special_chars = false; //Whether to include standard special characters. Default value: true
		
		// Generate the password so that the subscriber will have to check email...
		 $password = wp_generate_password( $password_length, $password_special_chars );
	
		$user_data = array(
			'user_pass'             => $password,   //(string) The plain-text user password.
			'user_login'            => $register_user['email'],   //(string) The user's login username.
			'user_email'            => $register_user['email'],   //(string) The user email address.
			'first_name'            => sanitize_text_field( $register_user['first_name'] ),   //(string) The user's first name.
			'last_name'             => sanitize_text_field( $register_user['last_name'] ),   //(string) The user's last name.
			'show_admin_bar_front'  => $show_admin_bar_front,   //(string|bool) Whether to display the Admin Bar for the user on the site's front end. Default true.
			'role'                  => $userRole,   //(string) User's role.
		//	'display_name'          => '',   //(string) The user's display name. Default is the user's username.
			'nickname'              => sanitize_text_field( $register_user['first_name'] ),   //(string) The user's nickname. Default is the user's username.
			'user_nicename'         => sanitize_text_field( $register_user['first_name'] ),   //(string) The URL-friendly user name.
		//	'user_url'              => '',   //(string) The user URL.
		//	'description'           => '',   //(string) The user's biographical description.
		//	'rich_editing'          => '',   //(string|bool) Whether to enable the rich-editor for the user. False if not empty.
		//	'syntax_highlighting'   => '',   //(string|bool) Whether to enable the rich code editor for the user. False if not empty.
		//	'comment_shortcuts'     => '',   //(string|bool) Whether to enable comment moderation keyboard shortcuts for the user. Default false.
		//	'admin_color'           => '',   //(string) Admin color scheme for the user. Default 'fresh'.
		//	'use_ssl'               => '',   //(bool) Whether the user should always access the admin over https. Default false.
		//	'user_registered'       => '',   //(string) Date the user registered. Format is 'Y-m-d H:i:s'.			
		//	'locale'                => '',   //(string) User's locale. Default empty.
		 
	);
	
		$user_id = wp_insert_user( $user_data );	//The newly created user's ID or a WP_Error object if the user could not be created.
		wp_new_user_notification( $user_id, $password, 'both' ); //Specifying $password is mandatory, else email will not be triggered for user
	
		return $user_id;
	}//End-of: function register_user()
	
	
	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	public function do_register_user() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$redirect_url = home_url( PAGE_SLUG_REGISTRATION  ); //'member-register'
			$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
				
			if ( ! get_option( 'users_can_register' ) ) {
				// Registration closed, display error
				$redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
				wp_redirect( $redirect_url );
				exit;
			}
			
			if( wp_verify_nonce($nonce , 'sdk101-user-flow-register-form') != 1 ){ // Perform nonce check for Login Form
				// Nonce check failed, display error
				$redirect_url = add_query_arg( 'register-errors', 'suspicious', $redirect_url );
				wp_redirect( $redirect_url );
				exit;
			}
			
			if($this->isGoogleCaptchActive){
				if ( ! $this->verify_recaptcha() ) {// Recaptcha check failed, display error
					$redirect_url = add_query_arg( 'register-errors', 'captcha', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}
			}
			
			if ( ! is_email($_POST['email'] ) ) {
				$errors = new WP_Error();
				$errors->add( 'email', $this->get_error_message( 'email' ) );
				$errors = join( ',', $errors->get_error_codes() );
				$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				wp_redirect( $redirect_url );
				exit;
			}
			
			$register_user = array(
					'email'			=>	$_POST['email'],
					'first_name'	=>	sanitize_text_field( $_POST['first_name'] ),
					'last_name'		=>	sanitize_text_field( $_POST['last_name'] ),
					'phone'			=>	sanitize_text_field( $_POST['phone'] ),
					'gender'		=>	sanitize_text_field( $_POST['gender'] )
				);
				$result = $this->register_user( $register_user ); // Function returns newly created user's ID or a WP_Error object if the user could not be created.
				
				if ( is_wp_error( $result ) ) {
					// Parse errors into a string and append as parameter to redirect
					$errors = join( ',', $result->get_error_codes() );
					$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				} else {
					//At this stage variable '$result' stores the ID of newly created user.
					
					update_user_meta( $result, 'p1_phone', $register_user['phone'] );
					update_user_meta( $result, 'p1_gender', $register_user['gender']);
					/*
					 * Add parameters from Registration Form to UserMeta table
					 *
					 * - 'sdk101_registration_form_fields_to_usermeta_action' is the action hook.
					 * - $result stored userId of newly created user. This argument is passed to the callback.
					 */
					do_action( 'sdk101_registration_form_fields_to_usermeta_action', $result );
					
					// Success, redirect to login page.
					$redirect_url = home_url( PAGE_SLUG_LOGIN);//'member-login' );
					$redirect_url = add_query_arg( 'registered', $register_user['email'], $redirect_url );
				}
			
	
			wp_redirect( $redirect_url );
			exit;
		}
	}//End-of: function do_register_user()
	
	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
		switch ( $error_code ) {
			case 'email':
				return get_option( 'personalize-error_message_registration_email' )?get_option( 'personalize-error_message_registration_email' ):__( 'The email address you entered is not valid.', 'personalize-login' );
			
			case 'email_exists':
				return get_option( 'personalize-error_message_registration_email_exists' )?get_option( 'personalize-error_message_registration_email_exists' ):__( 'An account exists with this email address.', 'personalize-login' );
			
			case 'closed':
				return get_option( 'personalize-error_message_registration_closed' )?get_option( 'personalize-error_message_registration_closed' ):__( 'New user registering is currently not allowed.', 'personalize-login' );
		
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
	 * An action function used to include the reCAPTCHA JavaScript file
	 * at the end of the page.
	 */
	public function add_captcha_js_to_footer() {
		if($this->isGoogleCaptchActive){
			echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
		}
	}//End-of: function add_captcha_js_to_footer()
	
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
	 * Filter function to customise the registration email sent to newly created user
	 */
	function customized_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
	 
		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );
		
		$login_url  = wp_login_url();
		
		$user_name = stripslashes( $user->first_name )." ".stripslashes( $user->last_name );
		$fromEmail = $this->setings['From-email'];
		$replyToEmail = $this->setings['ReplyTo-email'];
		$logoURL = $this->setings['logoURL'];
		$blogname = isset( $this->setings['blogname'] ) ? $this->setings['blogname'] : $blogname;

		$subject= "[".$blogname."] Login Details";
			//sprintf( '[%s] Login Details 1.', $blogname );
		$headers = array(	"From: ".$blogname." <$fromEmail>",
							"Reply-to: $replyToEmail",
							"Content-Type: text/html; charset=.get_bloginfo('charset')"
						);
						
		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) { return; }
		$resetPasswordURL = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
		
		ob_start();
    	include SDK101_USER_FLOW_PLUGIN_PATH.'/templates/email_welcome.php';
    	
    	$message = ob_get_clean();
		
		$wp_new_user_notification_email['subject'] = $subject;
		$wp_new_user_notification_email['headers'] = $headers;
		$wp_new_user_notification_email['message'] = $message;
		
		return $wp_new_user_notification_email;
	}
	
}//End-of: class Personalize_Registration_Plugin
?>