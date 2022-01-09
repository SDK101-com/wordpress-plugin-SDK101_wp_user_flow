<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Setting_Google_Captcha {
	
	private $page_title = '<div class="text-center"> User Flow Settings </div>';
    private $menu_title = 'User Flow setting';
    private $capability = 'manage_options';
    private $slug = 'user_flow_settings'; //Page URL Slug
	private $position = 10; //Menu Position
	
	/**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
		
		// Hook into the admin menu
    	add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );

        // Add Settings and Fields
    	add_action( 'admin_init', array( $this, 'setup_sections' ) );
    	add_action( 'admin_init', array( $this, 'setup_fields' ) );	
		 
    }//End-of: function __construct()
     
	public function create_plugin_settings_page() {
    	// Add the menu item and page
    	$callback = array( $this, 'plugin_settings_page_content' ); //calls function to print non-form content to the page
    	

    	add_options_page( $this->page_title, $this->menu_title, $this->capability, $this->slug, $callback, $this->position );
    }// function ends- create_plugin_settings_page

    public function plugin_settings_page_content() {?>
    	<div class="wrap">
    		<h2><?php echo $this->page_title;?></h2><?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  $this->admin_notice();
            } ?>
    		<form method="POST" action="options.php">
                <?php
                    settings_fields( $this->slug ); //A settings group name. This should match the group name used in register_setting()
                    do_settings_sections( $this->slug ); //output all the sections and fields added to $page with add_settings_section() and add_settings_field()
                    submit_button();
                ?>
    		</form>
    	</div> <?php
    }
    
    public function admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>settings have been updated!</p>
        </div><?php
    }

    public function setup_sections() {
        add_settings_section( 
			'sdk101_property_id1', //Section ID
			'<hr/>Google Captcha Settings', //Section Title
			array( $this, 'section_callback' ),  //Callback Function
			$this->slug ); //Page Slug
		
		add_settings_section( 
			'sdk101_property_id2', //Section ID
			'<hr/>Form Settings', //Section Title
			array( $this, 'section_callback' ),  //Callback Function
			$this->slug ); //Page Slug
    }

    public function section_callback( $arguments ) {
    	switch( $arguments['id'] ){
    		case 'sdk101_property_id1':
    			echo 'Settings related to Google Recaptcha';
    			break;
			case 'sdk101_property_id2':
    			echo 'Settings related to Login/Registration/Forget Password/Reset Password Forms';
    			break;
    		
    	}
    }

    public function setup_fields() {
       /* $fields = array(
        	array(
        		'uid' => 'awesome_text_field',
        		'label' => 'Sample Text Field',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'text',
        		'placeholder' => 'Some text',
        		'helper' => 'Does this help?',
        		'supplimental' => 'I am underneath!',
        	),
        	array(
        		'uid' => 'awesome_password_field',
        		'label' => 'Sample Password Field',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'password',
        	),
        	array(
        		'uid' => 'awesome_number_field',
        		'label' => 'Sample Number Field',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'number',
        	),
        	array(
        		'uid' => 'awesome_textarea',
        		'label' => 'Sample Text Area',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'textarea',
        	),
        	array(
        		'uid' => 'awesome_select',
        		'label' => 'Sample Select Dropdown',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'select',
        		'options' => array(
        			'option1' => 'Option 1',
        			'option2' => 'Option 2',
        			'option3' => 'Option 3',
        			'option4' => 'Option 4',
        			'option5' => 'Option 5',
        		),
                'default' => array()
        	),
        	array(
        		'uid' => 'awesome_multiselect',
        		'label' => 'Sample Multi Select',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'multiselect',
        		'options' => array(
        			'option1' => 'Option 1',
        			'option2' => 'Option 2',
        			'option3' => 'Option 3',
        			'option4' => 'Option 4',
        			'option5' => 'Option 5',
        		),
                'default' => array()
        	),
        	array(
        		'uid' => 'awesome_radio',
        		'label' => 'Sample Radio Buttons',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'radio',
        		'options' => array(
        			'option1' => 'Option 1',
        			'option2' => 'Option 2',
        			'option3' => 'Option 3',
        			'option4' => 'Option 4',
        			'option5' => 'Option 5',
        		),
                'default' => array()
        	),
        	array(
        		'uid' => 'awesome_checkboxes',
        		'label' => 'Sample Checkboxes',
				'description' => '',
        		'section' => 'our_first_section',
        		'type' => 'checkbox',
        		'options' => array(
        			'option1' => 'Option 1',
        			'option2' => 'Option 2',
        			'option3' => 'Option 3',
        			'option4' => 'Option 4',
        			'option5' => 'Option 5',
        		),
                'default' => array()
        	)
        );*/
		$fields = array(
        	
			//Google Recaptcha Settings
			array(
        		'uid' => 'personalize-login-recaptcha-activate',
        		'label' => 'Activate Recaptcha',
				'description' => 'Select the Checkbox and maintain details below to activate Google Captcha on Login, Registration and Forgot-Password Forms',
        		'section' => 'sdk101_property_id1',
        		'type' => 'checkbox',
        		'options' => array(
        			'activate' => 'Activate Captcha?',
        		),
                'default' => array()
        	),
			
			array(
        		'uid' => 'personalize-login-recaptcha-site-key',
        		'label' => 'reCAPTCHA site key',
				'description' => 'Get Recaptcha from: https://www.google.com/recaptcha/admin',
        		'section' => 'sdk101_property_id1',
        		'type' => 'text',
        	),
			array(
        		'uid' => 'personalize-login-recaptcha-secret-key',
        		'label' => 'reCAPTCHA secret key',
				'description' => '',
        		'section' => 'sdk101_property_id1',
        		'type' => 'text',
        	),
			
			//Form Error/Success Messages 
			array(
        		'uid' => 'personalize-error_message_registration_email',
        		'label' => 'Registration Email',
				'description' => "Used in Registration Forms. Evaluates the user's email-id",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'The email address you entered is not valid.',
				'supplimental' => 'Default: The email address you entered is not valid.',
        	),
			array(
        		'uid' => 'personalize-error_message_registration_email_exists',
        		'label' => 'Registration Email Exist',
				'description' => "Used in Registration Forms. Evaluates the user's email-id",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'An account exists with this email address.',
				'supplimental' => 'Default: An account exists with this email address.',
        	),
			array(
        		'uid' => 'personalize-error_message_registration_closed',
        		'label' => 'Registration Closed',
				'description' => "Used in Registration Forms. Shows this error when registration is closed.",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'New user registering is currently not allowed.',
				'supplimental' => 'Default: New user registering is currently not allowed.',
        	),
			array(
        		'uid' => 'personalize-error_message_empty_username',
        		'label' => 'Empty Username',
				'description' => 'Used in Login and Reset-Password Forms. When Username/email-id field is empty',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'You do have an email address, right?',
				'supplimental' => 'Default: You do have an email address, right?',
        	),
			array(
        		'uid' => 'personalize-error_message_empty_password',
        		'label' => 'Empty Password',
				'description' => 'Used in Login Form. When password field is empty',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'You need to enter a password to login.',
				'supplimental' => 'Default: You need to enter a password to login.',
        	),
			array(
        		'uid' => 'personalize-error_message_invalid_username',
        		'label' => 'Invalid Username',
				'description' => 'Used in Login and Reset-Password Forms. When invalid username/email-id is entered.',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => "We don't have any users with that email address. Maybe you used a different one when signing up?",
				'supplimental' => "Default: We don't have any users with that email address. Maybe you used a different one when signing up?",
        	),
			array(
        		'uid' => 'personalize-error_message_incorrect_password',
        		'label' => 'Incorrect Password',
				'description' => 'Used in Login Form. When incorrect password is entered.',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => "The password you entered wasn't quite right. {{ForgetPasswordURL}}",
				'supplimental' => "Default: The password you entered wasn't quite right. {{ForgetPasswordURL}} <br/> {{ForgetPasswordURL}} is replaced by: <a href='".wp_lostpassword_url()."'>Did you forget your password</a>?",
        	),
			array(
        		'uid' => 'personalize-error_message_invalidkey',
        		'label' => 'Invalid Email Activation Link',
				'description' => "Used to authenticate user's email-id. When user clicks on link sent via email, and if that link is not valid, this message is shown.",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => "The password reset link you used is not valid anymore.",
				'supplimental' => "Default: The password reset link you used is not valid anymore.",
        	),
			array(
        		'uid' => 'personalize-error_message_password_reset_mismatch',
        		'label' => 'Password Reset Mismatch',
				'description' => "Used in reset-password form. When password and confirm-password is not same during reset-password activity.",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => "The two passwords you entered don't match.",
				'supplimental' => "Default: The two passwords you entered don't match.",
        	),
			array(
        		'uid' => 'personalize-error_message_password_reset_empty',
        		'label' => 'Password Reset Empty',
				'description' => "Used in reset-password form. When either of password or confirm-password is empty during reset-password activity.",
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => "Sorry, we don't accept empty passwords.",
				'supplimental' => "Default: Sorry, we don't accept empty passwords.",
        	),
			array(
        		'uid' => 'personalize-error_message_captcha',
        		'label' => 'Empty Captcha(All Forms)',
				'description' => 'When Google Captch is not valid. This works if google captcha is active.',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'The Google reCAPTCHA check failed.',
				'supplimental' => 'Default: The Google reCAPTCHA check failed.',
        	),
			array(
        		'uid' => 'personalize-error_message_suspicious',
        		'label' => 'Suspicious(All Forms)',
				'description' => 'When invalid NONCE is submited.',
        		'section' => 'sdk101_property_id2',
        		'type' => 'text',
				//'placeholder' => 'Sorry, your login seems suspicious.',
				'supplimental' => 'Default: Sorry, your login seems suspicious.',
        	),
			
			
			
				
        );//End of array - $fields
		
    	foreach( $fields as $field ){

        	add_settings_field( 
				$field['uid'], //Field ID
				$field['label'], //Field Title. Displayed on Setting Pagge
				array( $this, 'field_callback' ), //Function callback
				$this->slug , //Setting Page URL
				$field['section'], //Page Section
				$field  //Array
			);
			
            register_setting( 
				$this->slug , 
				$field['uid'] 
			);
    	}//End of ForEach
    }//End of setup_fields()

    public function field_callback( $arguments ) {

        $value = get_option( $arguments['uid'] );
		//echo "<hr/>SDK-".(!empty($value));
		//print_r($value);
		
        if( ! $value ) {
            $value = $arguments['default'];
        }

        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
				printf( '<p>%s</p>', $arguments['description']);
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
				printf( '<p>%s</p>', $arguments['description']);
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $attributes = '';
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
				printf( '<p>%s</p>', $arguments['description']);
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
				printf( '<p>%s</p>', $arguments['description']);
                break;
        }

        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper );
			//printf( '<p>%s</p>', $arguments['description']);
        }

        if( $supplimental = $arguments['supplimental'] ){
            printf( '<p class="description">%s</p>', $supplimental );
			//printf( '<p>%s</p>', $arguments['description']);
        }

    }
	
}//End-of: class Plugin_Activation
?>