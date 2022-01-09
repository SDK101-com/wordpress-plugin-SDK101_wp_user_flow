<div class="container-sm text-center" >

	<?php	if ( $attributes['show_title'] ) : ?>
			<h3><?php _e( 'Pick a New Password', 'personalize-login' ); ?></h3>
    <?php	endif; 
	
	$message['css_class'] = "info";
	$message['html_content'] = __("Reset your password.",'personalize_login');
	
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
			
            <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
        		<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
				<input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
                
                <div class="form-floating mb-2">
					<input type="password" 
							class="form-control" 
                            name="pass1"
                            id="pass1" 
                            size="20"
                            value=""
                            autocomplete="off" >
                    <label for="pass1"><?php _e( 'New password', 'personalize-login' ) ?>&nbsp;<span class="required">*</span></label>
				</div>
                
                <div class="form-floating mb-2">
					<input type="password" 
							class="form-control" 
                            name="pass2"
                            id="pass2" 
                            size="20"
                            value=""
                            autocomplete="off" >
                    <label for="pass2"><?php _e( 'Repeat new password', 'personalize-login' ) ?>&nbsp;<span class="required">*</span></label>
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
	</div>
    
    <div>
    	<p><?php echo wp_get_password_hint(); ?></p>
    </div>
</div>