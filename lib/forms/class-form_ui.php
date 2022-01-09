<?php	if ( ! defined( 'ABSPATH' ) ){	exit;	}

class Form_UI{
	
	private $setting;
	
	/**
     * Initializes the plugin.
     *
     */
    public function __construct() {
		//initialise all setting variables
		$this->assignSetingsVariable();
		
		if($this->setting['activate_bootstrap']){
			//Enqueue Bootstrap.			
			add_action( 'admin_enqueue_scripts', array( $this, 'sdk101_user_flow_load_bootstrap' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'sdk101_user_flow_load_bootstrap' ) );
		}
		if($this->setting['activate_fontawesome']){
			//Enqueue FontAwesome 4.7.
			add_action( 'wp_enqueue_scripts', 'sdk101_user_flow_load_fontawesome' );
			add_action( 'admin_enqueue_scripts', 'sdk101_user_flow_load_fontawesome' );
		}//End-of: if($this->setting['activate_bootstrap'])
	}//End-of: function __construct()
	
	private function assignSetingsVariable(){
		$this->setings  = array(
				'activate_bootstrap'	=> false,
				'activate_fontawesome'	=> false, //true will load the bootstap if it is not loaded already, and false will just ignore
			);
	}//End-of: function assignSetingsVariable()
    
	/*
	 * Enqueue Bootstrap.
	 */
	private function sdk101_user_flow_load_bootstrap() {
		wp_enqueue_style( 'sdk101-wp-user-flow-bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', false, '5.0.2');
		wp_enqueue_style('sdk101-wp-user-flow-bootstrap-css');
		
		wp_enqueue_script( 'sdk101-wp-user-flow-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array(  ), '5.0.2', true );
	}//End-of: function sdk101_user_flow_load_bootstrap()
	
	/*
	 * Enqueue FontAwesome 4.7.
	 */
	 private function sdk101_user_flow_load_fontawesome() {
		 wp_enqueue_style( 'sdk101-wp-user-flow-fontawesom-css', 'https://use.fontawesome.com/releases/v5.7.1/css/all.css', false, '5.7.1' );
		 wp_enqueue_style('sdk101-wp-user-flow-fontawesom-css');
	 }//End-of: function sdk101_user_flow_load_fontawesome()
	
	
	public function setup_fields() {
		
		$form = array(
					
				);
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
		//End of array - $fields
		
    	foreach( $fields as $field ){

        	add_settings_field( 
				$field['uid'], //Field ID
				$field['label'], //Field Title. Displayed on Setting Pagge
				array( $this, 'form_UI_1_fields' ), //Function callback
				$this->slug , //Setting Page URL
				$field['section'], //Page Section
				$field  //Array
			);
			
    	}//End of ForEach
    }//End of setup_fields()
	
	public function form_UI_1( $arguments ){ ?>
		
		<div class="" style=" width:100%;max-width: 380px; margin:auto;">
		<div class="form-signin border border-2 rounded-3 p-3" id="lostpasswordform_pdiv">
			
            <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
				<?php	if(function_exists('wp_nonce_field')){	wp_nonce_field('sdk101-user-flow-password-lost-form');	}	?>
					
                    <div class="form-floating mb-2">
						<input type="email" 
							class="form-control" 
                            name="user_login"
                            id="user_login" 
                            placeholder="name@example.com" 
                            autocomplete="username"
                            size="20"
                            value="">
                         <label for="user_login"><?php _e( 'Email-Id' , 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
					</div>
        
					<div class="checkbox mb-2">
						<?php	if ( $attributes['recaptcha_site_key']  ):
                                echo '<!-- Show Google Recaptcha -->
                                    <div class="recaptcha-container">
                                        <div class="g-recaptcha  text-center" data-sitekey="'.$attributes['recaptcha_site_key'].'"></div>
                                    </div>';
                                endif;
                        ?>
					</div>

					<div class="d-grid">	
                        <input type="submit" 
                                name="submit"
                                id="submit"
                                class="btn btn-primary btn-lg"
                                value="<?php _e( 'Reset Password', 'personalize-login' ); ?>">
					</div> 
			</form>
		</div>
		
        <div class="d-grid mt-2">
			<a class="btn btn-outline-info btn-lg" href="<?php echo esc_url( home_url(PAGE_SLUG_LOGIN) ) ; ?>">
				<?php esc_attr_e( 'Log in', 'personalize-login' ); ?>
			</a>
		</div>
	</div>
    <?php
	}
	
    public function form_UI_1_fields( $arguments ) {

        $value = get_option( $arguments['uid'] );
				
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
	
}//End-of: class 
?>