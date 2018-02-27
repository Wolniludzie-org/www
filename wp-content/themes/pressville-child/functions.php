<?php add_action( 'after_setup_theme', 'lsvr_pressville_child_theme_setup' );
if ( ! function_exists( 'lsvr_pressville_child_theme_setup' ) ) {
	function lsvr_pressville_child_theme_setup() {

		/**
		 * Load parent and child style.css
		 *
		 * @link https://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme
		 */
		add_action( 'wp_enqueue_scripts', 'lsvr_pressville_child_enqueue_parent_styles' );
		if ( ! function_exists( 'lsvr_pressville_child_enqueue_parent_styles' ) ) {
			function lsvr_pressville_child_enqueue_parent_styles() {

				// Load parent theme's style.css
				$parent_version = wp_get_theme( 'pressville' );
				$parent_version = $parent_version->Version;
				wp_enqueue_style( 'lsvr-pressville-main-style', get_template_directory_uri() . '/style.css', array(), $parent_version );

				// Load child theme's style.css
				$child_version = wp_get_theme();
				$child_version = $child_version->Version;
				wp_enqueue_style( 'lsvr-pressville-child-style', get_stylesheet_directory_uri() . '/style.css', array(), $child_version );

			}
		}

		/* Add your code after this comment */
function create_post_type_glosowania() {
  register_post_type( 'glosowania',
    array(
      'labels' => array(
        'name' => __( 'Głosowania' ),
        'singular_name' => __( 'Głosowanie' )
      ),
      'public' => true,
      'has_archive' => true,
	  'supports' => array('title', 'editor', 'thumbnail'),
    )
  );
}
add_action( 'init', 'create_post_type_glosowania' );		
	
	
function posts_for_current_author($query) {
    global $pagenow;
 
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
 
    if( !current_user_can( 'edit_others_posts' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');	

function mybp_hide_some_profile_fields( $retval ) {
	if(  bp_is_user_profile_edit() ) {		
		$retval['exclude_fields'] = '2,3,4,6';	//multiple field ID's should be separated by comma
	}	
	
	return $retval;
}
add_filter( 'bp_after_has_profile_parse_args', 'mybp_hide_some_profile_fields' );
		
function mybp_redirect_profile_access(){
        if (current_user_can('manage_options')) return '';

        if (strpos ($_SERVER ['REQUEST_URI'] , 'wp-admin/profile.php' )) {
			if( ! current_user_can( 'manage_options' ) && function_exists( 'bp_core_get_user_domain' ) )
				exit( wp_safe_redirect( bp_core_get_user_domain( get_current_user_id() ) ) );
			else
				exit(wp_redirect ( home_url())); // to page like: example.com/my-profile/
        }
}		
add_action ('init' , 'mybp_redirect_profile_access');

add_filter( 'wp_nav_menu_args', function( Array $args ) {
    if ( is_user_logged_in() )
        $args['menu_class'] .= '  logged-in';
	else 
		$args['menu_class'] .= '  logged-out';
    return $args;
} );


	}
}

?>