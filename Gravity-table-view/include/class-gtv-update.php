<?php
if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}

 if ( $_POST['action'] == 'update-user' ) {
  update_info();
global $current_user, $wp_roles;
$current_url==strtok($_SERVER["REQUEST_URI"],'?'); 
$email =  $_POST['gtv_email'];
$exists = email_exists( $email );





 if ( $exists ){
	 //echo "exist";
     if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {
		
	  if ( count($error) == 0 ) {
		  do_action('edit_user_profile_update', $_POST['user_id']);
			$get_email_check = $email ==  wp_get_current_user()->user_email ? "?updated=true'" :"?updated=true&email=exist'" ;
		  $updated = isset($_GET['updated'])?"":$get_email_check ;
		  
		  if($_POST['gtv_password'] != ""){
			   session_start();
				$_SESSION["gtv_password"] = $_POST['gtv_password'] ;
			  $_SESSION["gtv_current_password"] = $_POST['gtv_current_password'] ;
		  }
		 $email_query =  !empty( $_POST['gtv_password'] )?"":"&empty_pass=true'";
	 	$email_query = empty( $updated)?"?empty_pass=true'":"&empty_pass=true'";
		wp_redirect($current_url.$updated. $email_query);
	  }   
	 }
  }
  else{
	  // Force update our username (user_login)
global $wpdb;
$tablename = $wpdb->prefix . "users";
$email_query ="";
	  if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {
		    if ( !empty( $_POST['gtv_email'] ) ){
		 // if( !email_exists(  $_POST['gtv_email']  )) 
			//update_user_meta($_POST['user_id'], 'gtv_email', esc_attr( $_POST['gtv_email'] ) );
			 	$tablename = $wpdb->prefix . "users";

			// method 1
			$sql = $wpdb->prepare("UPDATE {$tablename} SET user_email=%s WHERE ID=%d", $_POST['gtv_email'], $_POST['user_id']);
			$wpdb->query($sql);

			// method 2
			//$wpdb->update( $tablename, array( 'user_email' => $_POST['gtv_email'] ), array( 'ID' => $user_id ) );
	   }
		  update_info();
	 
	   	
  if ( count($error) == 0 ) {
	  do_action('edit_user_profile_update', $_POST['user_id']);
	  $updated = isset($_GET['updated'])?"":"?updated=true'" ;
	  
	  if( $_POST['gtv_password'] != ""){
			 session_start();
				 $_SESSION["gtv_password"] = $_POST['gtv_password'] ;
			  $_SESSION["gtv_current_password"] = $_POST['gtv_current_password'] ;
				
	}
	$email_query =  !empty( $_POST['gtv_password'] )?"":"&empty_pass=true'";
	 	$email_query = empty( $updated)?"?empty_pass=true'":"&empty_pass=true'";
		wp_redirect($current_url.$updated.$email_query);
//	header("Location: ".$current_url.$updated);
  }   
}
  } 
  }
  function update_info()
  {
	 global $current_user;
	  
	  if ( !empty( $_POST['gtv_first_name'] ) )
        update_user_meta($_POST['user_id'], 'gtv_first_name', esc_attr( $_POST['gtv_first_name'] ) );
	if ( !empty( $_POST['gtv_last_name'] ) )
        update_user_meta($_POST['user_id'], 'gtv_last_name', esc_attr( $_POST['gtv_last_name'] ) );
	if ( !empty( $_POST['phone_number'] ) )
        update_user_meta($_POST['user_id'], 'phone_number', esc_attr( $_POST['phone_number'] ) );
	if ( !empty( $_POST['billing_country_front'] ) )
        update_user_meta($_POST['user_id'], 'gtv_billing_country', esc_attr( $_POST['billing_country_front'] ) );
	if ( !empty( $_POST['gtv_websites'] ) )
        update_user_meta($_POST['user_id'], 'gtv_websites', esc_attr( $_POST['gtv_websites'] ) );
	
	 if ( !empty( $_POST['gtv_company_name'] ) )
        update_user_meta($_POST['user_id'], 'gtv_company_name', esc_attr( $_POST['gtv_company_name'] ) );
	 if ( !empty( $_POST['gtv_building_name'] ) )
        update_user_meta($_POST['user_id'], 'gtv_building_name', esc_attr( $_POST['gtv_building_name'] ) );
	 if ( !empty( $_POST['gtv_addres_line_one'] ) )
        update_user_meta($_POST['user_id'], 'gtv_addres_line_one', esc_attr( $_POST['gtv_addres_line_one'] ) );
	 if ( !empty( $_POST['gtv_addres_line_two'] ) )
        update_user_meta($_POST['user_id'], 'gtv_addres_line_two', esc_attr( $_POST['gtv_addres_line_two'] ) );
	 if ( !empty( $_POST['gtv_city_state'] ) )
        update_user_meta($_POST['user_id'], 'gtv_city_state', esc_attr( $_POST['gtv_city_state'] ) );
	 if ( !empty( $_POST['gtv_postcode_zipcode'] ) )
        update_user_meta($_POST['user_id'], 'gtv_postcode_zipcode', esc_attr( $_POST['gtv_postcode_zipcode'] ) );
	 if ( !empty( $_POST['gtv_coutry'] ) )
        update_user_meta($_POST['user_id'], 'gtv_coutry', esc_attr( $_POST['gtv_coutry'] ) ); 
	
	
/* 		$user = get_user_by( 'login', $current_user->user_login);
		if ( $user && wp_check_password( 'justest', $user->data->user_pass, $user->ID ) ) {
			echo "That's it";
		} else {
			echo "Nope";
		}
		echo  $user->data->user_pass ; */
		
	    
	
		 
		
		
			
	  
	
  }
	






?>