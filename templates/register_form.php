<div id="register-form" class="container-sm text-center">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Register', 'personalize-login' ); ?></h3>
    <?php endif; 
	
	$message['css_class'] = "info";
	$message['html_content'] = '';
	
	foreach($attributes as $key => $value){
		switch($key){
			case 'errors': //Show errors if there are any
					if( count( $value ) > 0  ):
						$message['css_class'] = "danger";
						$message['html_content'] = '';
						foreach ( $value as $error ) :
							$message['html_content'] .= $error. "<br/>";
						endforeach;
					endif; 
				break;
			default:
		}//End-of: switch($key)
	}//End-of: foreach($attributes as $key => $value)
	
			if($message['html_content']):?>
				<div class="alert alert-<?php echo $message['css_class']; ?>" role="alert">
					<?php	echo $message['html_content'];	?>
				</div>
	<?php	endif;	?>
    
	<div class="" style=" width:100%;max-width: 380px; margin:auto;">
		<div class="form-signin border border-2 rounded-3 p-3" id="password-reset-form">
			
            <form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
    	<?php	if(function_exists('wp_nonce_field')){	wp_nonce_field('sdk101-user-flow-register-form');	}	?>
        
        
        <!-- Email Address-->
        <div class="form-floating mb-2">
                    <input type="text" 
                        class="form-control" 
                        name="email"
                        id="email" 
                        placeholder="name@example.com" 
                        autocomplete="email"
                        size="20"
                        value="">
                     <label for="email"><?php _e( 'Email-Id' , 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
                </div>
                
        <!-- First Name-->
        <div class="form-floating mb-2">
                    <input type="text" 
                        class="form-control" 
                        name="first_name"
                        id="first_name"
                        placeholder=" First Name"
                        autocomplete="first_name">
                     <label for="first_name"><?php _e( 'First name', 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
                </div>
        

        <!-- Last Name-->
        <div class="form-floating mb-2">
                    <input type="text" 
                        class="form-control" 
                        name="last_name"
                        id="last_name"
                        autocomplete="last_name"
                        placeholder="Last Name">
                     <label for="last_name"><?php _e( 'Last name', 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
        </div>
        
       <!-- Gender-->
        <div class="form-floating mb-2 border border-1 rounded-2 text-start p-3" style="border-color:#d1d3e2 !important;">
        	<div class="form-check form-check-inline">
            	<label class="form-check-label"  for="gender">	<?php _e( 'Gender', 'personalize-login' ); ?><span class="wpsp-required">*</span></label>
            </div>
            <div class="form-check form-check-inline">
  				<input class="form-check-input" type="radio" name="gender" id="father" value="Male">
  				<label class="form-check-label" for="father">Male</label>
			</div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" id="mother" value="Female">
              <label class="form-check-label" for="mother">Female</label>
            </div>
        </div>
        
        <!-- Contact Number-->
        <div class="form-floating mb-2">
                    <input type="tel" 
                        class="form-control" 
                        name="phone"
                        id="phone"
                        autocomplete="phone"
                        placeholder="0000000000"
                        pattern="[0-9]{10}">
                     <label for="phone"><?php _e( 'Contact Number', 'personalize-login' ); ?>&nbsp;<span class="required">*</span></label>
        </div>
        
		<p class="form-row">
            <?php _e( 'Note: A link will be sent to your email address to generate your password.', 'personalize-login' ); ?>
        </p>

        
        	
        <!-- Show Google Recaptcha -->
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
                                value="<?php _e( 'Register', 'personalize-login' ); ?>">
					</div> 
    </form>
    </div>
		<div class="d-grid mt-2">
			<a class="btn btn-outline-info btn-lg" href="<?php echo esc_url( home_url(PAGE_SLUG_LOGIN) ) ; ?>">
				<?php esc_attr_e( 'Log in', 'personalize-login' ); ?>
			</a>
		</div>
	</div>
</div>
