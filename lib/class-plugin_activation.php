<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-2-new-user-registration--cms-23810

class Plugin_Activation {
 
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
	
	 }//End-of: function __construct()
     
	
	/**
	 * Plugin activation hook.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 */
	public static function plugin_activated() {
		// Information needed for creating the plugin's pages
		$page_definitions = array(	
					//Page-1: Slug of this page is "member-login"
					//'member-login'
					PAGE_SLUG_LOGIN => array(
						'title' => __( 'Sign In', 'personalize-login' ),
						'content' => '[custom-login-form]'
					),
					
					//Page-2: Slug of this page is "member-account"
					/*
					'member-account' => array(	
						'title' => __( 'Your Account', 'personalize-login' ),
						'content' => '[account-info]'
					),
					*/
					
					//Page-3: Slug of this page is "member-register"
					//'member-register'
					PAGE_SLUG_REGISTRATION => array(
						'title' => __( 'Register', 'personalize-login' ),
						'content' => '[custom-register-form]'
					),
					//Page-4: Slug of this page is "member-password-lost"
					PAGE_SLUG_PASSWORD_LOST => array(
						'title' => __( 'Forgot Your Password?', 'personalize-login' ),
						'content' => '[custom-password-lost-form]'
					),
					//Page-5: Slug of this page is "password-reset"
					PAGE_SLUG_PASSWORD_RESET => array(
						'title' => __( 'Pick a New Password', 'personalize-login' ),
						'content' => '[custom-password-reset-form]'
					)
			);
	 
		foreach ( $page_definitions as $slug => $page ) {
			// Check that the page doesn't exist already
			$query = new WP_Query( 'pagename=' . $slug );
			if ( ! $query->have_posts() ) {
				// Add the page using the data from the array above
				wp_insert_post(
					array(
							'post_content'   => $page['content'],
							'post_name'      => $slug,
							'post_title'     => $page['title'],
							'post_status'    => 'publish',
							'post_type'      => 'page',
							'ping_status'    => 'closed',
							'comment_status' => 'closed',
					)
				); //End-of: wp_insert_post
			}//End-of: if ( ! $query->have_posts() )
		}//End-of: foreach()
		
		Plugin_Activation::update_usermeta_for_all_users( get_users( ));
	}//End-of: function plugin_activated()
	
	/*
	 * Function to hide WordPress top Admin bar.
	 * This function is called during activation and updaes files "show_admin_bar_front" in user table
	 *
	 * @param $usersAr is a multidimentional arra which stores details of all users
	 */
	 public function update_usermeta_for_all_users( $usersAr = null ){
		 
		 foreach($usersAr as $user){
			 
			// if($user_roles[0] != 'administrator'){
				 update_user_meta( $user->ID, 'show_admin_bar_front', 'false' );
			// }
		}//End-of: forcash
		 
	 }//End-of: update_usermeta_for_all_users()
	
	
	
	
}//End-of: class Plugin_Activation
?>