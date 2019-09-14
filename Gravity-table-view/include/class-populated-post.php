<?php
class populated_post
{
   
    public function __construct()
    {
		add_filter('gform_field_value_get_gtv_first_name',array($this, 'get_gtv_first_name'));
		add_filter('gform_field_value_get_gtv_last_name',array($this, 'get_gtv_last_name'));
		add_filter('gform_field_value_get_gtv_email',array($this, 'get_gtv_email'));
		 add_filter('gform_field_value_get_gtv_websites',array($this, 'get_gtv_websites'));
		add_filter('gform_field_value_get_gtv_phone_number',array($this, 'get_gtv_phone_number'));
		add_action( 'wp_footer',array($this, 'get_gtv_billing_country'), 100 );
	}
		
	
function get_gtv_first_name($user){
 global $current_user;
    $get_gtv_first_name = !is_user_logged_in() ?"":get_the_author_meta( "gtv_first_name", $current_user->ID );

    return $get_gtv_first_name;
}	
function get_gtv_last_name($user){
 global $current_user;
    $get_gtv_last_name = !is_user_logged_in() ?"":get_the_author_meta( "gtv_last_name", $current_user->ID );

    return $get_gtv_last_name;
}
function get_gtv_email($user){
 global $current_user;
    $get_gtv_email = !is_user_logged_in() ?"":$current_user->user_email ;

    return $get_gtv_email;
}
function get_gtv_billing_country($user) {
	 	global $current_user;
		$get_selected=!is_user_logged_in() ?"":get_the_author_meta( "gtv_billing_country", $current_user->ID);
		
    echo '<script>jQuery(".country_dropdown select").val("'.$get_selected.'");</script>'; 
}
function get_gtv_phone_number($user){
	global $current_user;
    $get_gtv_phone_number = !is_user_logged_in() ?"":get_the_author_meta( "phone_number", $current_user->ID );

    return $get_gtv_phone_number;
}
function get_gtv_websites($user){
	global $current_user;
    $get_gtv_websites =  !is_user_logged_in() ?"":get_the_author_meta( "gtv_websites", $current_user->ID );

    return $get_gtv_websites;
}
	}
if( !is_admin() )
    $populated_post = new populated_post()
?>