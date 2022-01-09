<div class="container-sm text-center">
    
	<?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Sign In', 'personalize-login' ); ?></h2>
    <?php endif; ?>
   
   <!-- Show errors if there are any -->
	<?php
		$message['css_class'] = "success";		
		$message['html_content'] = "";
		
		foreach($attributes as $key => $value){
			switch($key){
				case 'errors': //Show errors if there are any
								if( count( $value ) > 0  ):
									$message['css_class'] = "danger";
									foreach ( $value as $error ) :
										$message['html_content'] .= $error. "<br/>";
									endforeach;
								endif; 
					break;
				case 'logged_out': //Show logged out message if user just logged out
						if(!empty($value)){
							$message['html_content'] .= __( 'You have signed out. Would you like to sign in again?', 'personalize-login' );
						}
					break;
				case 'checkemail': //Show logged out message if user just logged out
						if($value == 'confirm'){
							$message['html_content'] .= __( 'A Password reset link is sent to your registered email-id.', 'personalize-login' );
						}
					break;
				case 'registered': //Show Succesfully Registered message
						if(!empty($value)){
							$message['html_content'] .= sprintf(
                    			__( 'You have successfully registered to <strong>%s</strong>. Check email address <strong>%s</strong> for details on how to login.' , 'personalize-login' ),
                    			get_bloginfo( 'name' ),
								$value
                			);
						}
					break;
				case 'lost_password_sent': //Show message after reset password link is sent
						if(!empty($value)){
							$message['html_content'] .= __( 'You have signed out. Would you like to sign in again?', 'personalize-login' ); 
						}
					break;
				
				case 'password_updated': //Show logged out message if user just logged out
						if(!empty($value)){
							$message['html_content'] .= __( 'Your password has been changed. You can sign in now.', 'personalize-login' ); 
						}
					break;
				default: 
				
			}//end-of: switch()
		}//End-of: foreach
	
		if($message['html_content']):?>
			<div class="alert alert-<?php echo $message['css_class']; ?>" role="alert">
				<?php	echo $message['html_content'];	?>
			</div>
	<?php endif;
	
	/*wp_login_form(
            array(
                'label_username'	=>	__( 'Email', 'personalize-login' ),	//The text label for the user name field.
                'label_log_in'		=>	__( 'Sign In', 'personalize-login' ),
                'redirect'			=>	$attributes['redirect'],	//The URL to redirect to after successful login.
				'remember'			=>	true,	//Whether the "Remember Me" checkbox should be shown or not.
				'value_remember'	=>	true,	//Whether the "Remember Me" checkbox should be checked initially or not.
				'label_password'	=>	__( 'Password', 'personalize-login' ),	//The text label for the password field.
				'label_remember'	=>	__( 'Remember Me', 'personalize-login' ),	//The text label for the "Remember Me" checkbox.
				'label_log_in'		=>	__( 'Log In', 'personalize-login' ),	//The text label for the "Log In" button.
				'id_username'		=>	__( 'user_login', 'personalize-login' ),	//The HTML id for the user name field.
				'id_password'		=>	__( 'user_pass', 'personalize-login' ),	//The HTML id for the password field.
				'id_remember'		=>	__( 'rememberme', 'personalize-login' ),	//The HTML id for the "Remember Me" checkbox.
				'id_submit'			=>	__( 'wp-submit', 'personalize-login' ),	//The HTML id for the submit button.
            )
        );
	*/
    ?>
    <div class="" style=" width:100%;max-width: 380px; margin:auto;">
    	
        <div class="form-signin border border-2 rounded-3 p-3">
            <form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
                <?php	if(function_exists('wp_nonce_field')){	wp_nonce_field('sdk101-user-flow-login-form');	}	?>
                
                <div class="form-floating mb-2">
                    <input type="email" 
                        class="form-control" 
                        name="log"
                        id="user_login" 
                        placeholder="name@example.com" 
                        autocomplete="username"
                        size="20"
                        value="">
                     <label for="user_login"><?php _e( 'Email-Id' , 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
                </div>
                
                <div class="form-floating">
                    <input type="password" 
                            name="pwd" 
                            id="user_pass" 
                            autocomplete="current-password" 
                            class="form-control" 
                            placeholder="Password"
                            size="20">
                    <label for="user_pass"><?php esc_html_e( 'Password', 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
                </div>
                
                <?php
                    /**
                     * Fires following the 'Password' field in the login form.
                     *
                     * @since 2.1.0
                     */
                    do_action( 'login_form' );
                ?>
    
                <div class="checkbox mb-2">
                    <div class="row align-items-start">
                        <label class="col">
                            <input type="checkbox" 
                                name="rememberme"
                                id="rememberme" 
                                value="forever"
                            /> 
                            <span><?php esc_html_e( 'Remember me', 'personalize-login' ); ?></span>
                        </label>
                        
                        <a class="col" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'personalize-login' ); ?></a>
                    </div>
                    
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
                            name="wp-submit"
                            id="wp-submit"
                            class="btn btn-primary btn-lg"
                            value="<?php esc_attr_e( 'Log in', 'personalize-login' ); ?>">
                </div>            
            </form>
        </div>
        
        <div>
			<?php	if ( get_option( 'users_can_register' ) ) :	?>
                		<div class="d-grid mt-2">
                    		<a class="btn btn-outline-info btn-lg" href="<?php echo esc_url( wp_registration_url() ) ; ?>">
                        		<?php _e( 'Register', 'personalize-login' ); ?>
                    		</a>
                		</div>
        	<?php endif;?>
		</div>
</div>
