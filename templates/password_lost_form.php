<div class="container-sm text-center" >

	<?php	if ( $attributes['show_title'] ) : ?>
    			<h3><?php _e( 'Forgot Your Password?', 'personalize-login' ); ?></h3>
    <?php	endif;
	
	$message['css_class'] = "info";
	$message['html_content'] = __("Enter your email address and we'll send you a link you can use to pick a new password.",'personalize_login');
	
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
</div>