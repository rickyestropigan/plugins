<?php
/*
* Plugin Name: Gravity Table Entries with Paypal
* Description: View your Gravity paypal data.
* Version: 0.0.1
* Author: Ricky
* Author URI: http://sample.com/
*/


// add_action( 'wp', GFPayPal::maybe_thankyou_page(), 5 ); 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly
// Example 1 : WP Shortcode to display form on any page or post.

// include( plugin_dir_path( __FILE__ ) . 'includes/admin-view.php');
/*  
 define( 'PLUGIN_ROOT_DIR', plugin_dir_path( __FILE__ ) );
include( plugin_dir_path( __FILE__ ) .  'includes/admin-view.php'); */
if( ! class_exists( 'Gravity_table_update' ) )
{
	 
	include_once plugin_dir_path( __FILE__ ) .'include/gtv_update.php';
}


 
 $updater = new Gravity_table_update( __FILE__ );
$updater->set_username( 'neroshin' );
$updater->set_repository( 'gravity-view-table' );
$updater->authorize( '8457e670241d973682fdb9fdd052d63c710899c3' ); // Your auth code goes here for private repos
$updater->initialize();// initialize the  */

include_once plugin_dir_path( __FILE__ ) .'include/admin-view.php';
include_once plugin_dir_path( __FILE__ ) .'include/class-populated-post.php';
include_once plugin_dir_path( __FILE__ ) .'include/gravity-forms-better-pre-submission-confirmation.php'; 
include_once plugin_dir_path( __FILE__ ) .'include/gtv_update.php';
GFForms::include_payment_addon_framework();

if( !class_exists( 'GravityView_Extension' ) ) {

		if( class_exists('GravityView_Plugin') && is_callable(array('GravityView_Plugin', 'include_extension_framework')) ) {
			GravityView_Plugin::include_extension_framework();

			echo "GravityView_Extension";
			//include( plugin_dir_path( __FILE__ ) .  'includes/admin-view.php'); 
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			//include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}

// echo $my_settings_page->title_callback(); 
function wpdocs_theme_name_scripts() {
wp_enqueue_style( 'style-table', plugins_url('asset/Gravity-table-entries.css', __FILE__) );
wp_enqueue_style( 'style-tablesaw', plugins_url('asset/tablesaw.css', __FILE__)) ;
wp_enqueue_script( 'datatables', '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array( 'jquery' ) );
wp_enqueue_style( 'datatables-style', '//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css' );
wp_enqueue_script( 'script-pagination',plugins_url('asset/js/pagination-fron-end.js', __FILE__), array(), '1.0.0', true ); 
wp_enqueue_script( 'script-data-loadfont','//filamentgroup.github.io/demo-head/loadfont.js', array(), '1.0.0', true ); 
wp_enqueue_script( 'script-data-tablesaw',plugins_url('asset/js/tablesaw.js', __FILE__), array(), '1.0.0', true ); 
wp_enqueue_script( 'script-data-tablesaw-init',plugins_url('asset/js/tablesaw-init.js', __FILE__), array(), '1.0.0', true ); 
 wp_enqueue_script( 'script-bootstrap-validator',"https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.js", array(), '0.11.9', true ); 
wp_localize_script( 'script-pagination', 'postlove', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));
wp_localize_script( 'script-pagination', 'payment', array(
	'ajax_url' => admin_url( 'admin-ajax.php' )
));

}

function table_creation($attrs){
/* 
if ( $str = rgget( 'gf_paypal_return' ) ) {
			
			
			$str = base64_decode( $str );

			parse_str( $str, $query );
			if ( wp_hash( 'ids=' . $query['ids'] ) == $query['hash'] ) {
				list( $form_id, $lead_id ) = explode( '|', $query['ids'] );

				$form = GFAPI::get_form( $form_id );
				$lead = GFAPI::get_entry( $lead_id );

				if ( ! class_exists( 'GFFormDisplay' ) ) {
					require_once( GFCommon::get_base_path() . '/form_display.php' );
				}

				$confirmation = GFFormDisplay::handle_confirmation( $form, $lead, false );
				$table_data =  $confirmation;
			 	 if ( is_array( $confirmation ) && isset( $confirmation['redirect'] ) ) {
					
					header( "Location: {$confirmation['redirect']}" );
					exit;
				}  
				GFFormDisplay::$submission[ $form_id ] = array( 'is_confirmation' => true, 'confirmation_message' => $confirmation, 'form' => $form, 'lead' => $lead );
			}
			return $table_data;
} 
 */
global $wpdb;
 $current_user = wp_get_current_user();

 $is_empty_title = true;
$forms_id = RGFormsModel::get_forms(1, "title");
$form = GFAPI::get_form(1);
 $current_user = wp_get_current_user();
$forms_get_leads = array(); 
$options = get_option( 'my_option_name' );
 $title_display = explode(",",  $options['title']);



 
add_action( 'wp_enqueue_scripts', 'theme_prefix_enqueue_script' );
 add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );
if(!isset($attrs['status']))
{
	$attrs['status'] = "all";
}
 

 foreach ( $forms_id as $print_active )
{	
    $lead['payment_status'] = 'Approved';
	$forms_get_leads =  array_merge_recursive( $forms_get_leads,GFFormsModel::get_leads($print_active->id));
	 
} 
    $fields = $form["fields"];
 // echo "<pre>";
 
 // print_r( $fields );
   // echo "</pre>";  
	
$forms_get_leads = array_filter( $forms_get_leads );
$value_recursive = apply_filters( 'hook_filter_recursive', $forms_get_leads );


/* 
    echo "<pre>";
    print_r( $value_recursive );
  echo "</pre>";  */     

usort($value_recursive, "Sortbydate");
/*  print_r($value_recursive);
  echo "</pre>";  */

$table_data .=" <h5 id='welcome'>Welcome back ".$current_user->user_login."!</h5>";
$table_data .=" <div id='pagination'></div>";
$table_data .="<div class='table-responsive'><table id='gravity-table' class='tablesaw tablesaw-swipe display dataTable' data-tablesaw-mode='swipe' cellspacing='0' width='100%' role='grid' aria-describedby='example_info' style='width: 100%;'>";
$table_data .= "<thead>";
 $label = "Order Number";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label title defaultSort' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='persist' >$label</th>";  
$label = "Service ";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label ' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='1'>$label</th>";  
/* $label = "User";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-sortable-default-col data-tablesaw-priority='2'>$label</th>";  */
$label = "Email";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label defaultSort' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='2'>$label</th>"; 
$label = "Status";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='3'>$label</th>"; 
$label = "Date Payment";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label ' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='4' style='width: 60px;'>$label</th>"; 
$label = "Amount";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='5'>$label</th>"; 
$label = "Payment";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='6'>$label</th>"; 
$label = "Action";
$class_label = "header-" . str_replace(" ", "-", strtolower($label));
$table_data .= "<th  class='sort $class_label' data-sort='sort-$i' scope='col' data-tablesaw-sortable-col data-tablesaw-priority='7'>$label</th>"; 
$table_data .= "</thead>";
$table_data .= "</tbody>";
if (!is_user_logged_in() ) 
{
	
}
else{
	apply_filters( 'gform_submit_button' ,"",$form );
	  foreach ( $value_recursive as $data )   
	  {
		  	$value_recursive = apply_filters( 'hook_RGCurrency', $data->payment_amount );
			 $author_obj = get_user_by('id', $data->created_by);
			$form_title = apply_filters( 'hook_get_the_form_title', $data->form_id );
			
			
		    $email = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND  `field_number` LIKE  '31'" );
		

		
			
			if(empty($email)){
					// echo ."<br>";
					if($data->form_id == 6){
							
									$email = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND  `field_number` LIKE  '160'" );
					}
					else if($data->form_id == 9){
							$email = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND  `field_number` LIKE  '162'" );
					}
					else if($data->form_id == 13){
						$email = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND  `field_number` LIKE  '162'" );
					}
					else if($data->form_id == 1){
							$email = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND  `field_number` LIKE  '167'" );
					}
					else{}
				   // $email = empty($email) ?$email2:$email;
					// $email = empty($email) ?$email3:$email;
					// $email = empty($email) ?$email4:$email;
			}
		 
		    $get_email="";
		    $get_username="";
		   $keyfield_id = "";
			 
			foreach ($data as $keyfield => $valuefield) {
				// echo $msg->date;      // shows 2013-01-28 08:35:59
			//	echo $fields[170]."<br>";
			    if( get_fields($data->form_id ,$keyfield) == "Username" || get_fields($data->form_id ,$keyfield) == "username")
				{
					$keyfield_id= $keyfield;
				}
			}
		  $username_nd = $wpdb->get_results( "SELECT value FROM  `onlinema_rg_lead_detail` WHERE  `lead_id` LIKE  '$data->id' AND `field_number` LIKE  '".$keyfield_id."'" );
		   /* 
	    	   echo "<pre>";
			  print_r(  $username_nd);
		  echo "</pre>";   */  
			foreach ($email as $object) 
			{ 
				$get_email = array_key_exists($object->value,$object)?"":$object->value;
			}
			foreach ($username_nd as $object)
			{ 
				$get_username = array_key_exists($object->value,$object)?"":$object->value;
			}
		$form_allowed =  $data->payment_status == "" ?"false" :"true";
		
			//echo $get_username ."==". $current_user->user_login."=".($get_username == $current_user->user_login)."<br>" ;
			if( $form_allowed == "true" )
			{
			
			if($author_obj->user_login ==  $current_user->user_login || $get_username == $current_user->user_login )
			{
		//	GFAPI::update_entry_property( $data->id, 'payment_status', 'Processing' );
	/* 	echo "<pre>";
		
print_r(get_feeds( $data->form_id));
echo "</pre>"; */  



$submission_data =Array(
	"form_title" => "Blogger Outreach",
    "email"=> "re4227-5@gmail.com",
    "address" => "",
    "address2" => "",
    "city" => "",
    "state" => "",
    "zip" => "",
    "country"=> "",
    "payment_amount" => 40,
    "setup_fee" => 0,
    "trial" => 0,
	"line_items" => Array ( "0" => Array ( 'id' => 96,'name' => 'DA 10+ Websites', 'description'=> "",'quantity'=>1,'unit_price'=> 40,  'options' => ""))
 );



add_filter( 'gform_paypal_request', 'update_url', 10, 5 );
 if($attrs['status'] == $data->payment_status  ){
	 
		 gf_apply_filters( 'gform_paypal_request', $data->form_id, "http://onlinemarketing.guru/booking/", $form, GFAPI::get_entry( $data->id ), paypal_get_feeds( $data->form_id),$submission_data ) ;
				$get_setting = get_option('my_option_name') ;
				// print_r($get_setting);
			//	$payment = $data->payment_method!=""?$data->payment_method:'';
		//	$payment = $data->payment_method!=""?$data->payment_method:"<a href = '".gf_apply_filters( 'gform_paypal_request', $data->form_id, "http://onlinemarketing.guru/booking/", $form, GFAPI::get_entry( $data->id ), paypal_get_feeds( $data->form_id),$submission_data ) ."'>Pay Now</a>";
			
$payment = $data->payment_method!=""?$data->payment_method:'<a class="more-info paynow" style="cursor: pointer;" onclick = "redirect('."'http://onlinemarketing.guru/booking/'".','. $data->id .','. $data->form_id.',\''. $get_email.'\');">Pay Now</a>';


			$table_data .= '<tr>';  
				$table_data .= '<td class="title" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');"><span><a >'.$data->id.'</a></span></td>'; 
				$table_data .= '<td ><span>'.$form_title.'</span></td>';
				//$table_data .= '<td ><span>'.$author_obj->user_login.'</span></td>';
				$table_data .= '<td ><span>'.$get_email.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_status.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_date.'</span></td>';
				//$table_data .= '<td ><span>'.$data->transaction_id.'</span></td>';
				$table_data .= '<td ><span>'.$value_recursive.'</span></td>';
				$table_data .= '<td ><span>'.$payment.'</span></td>';
				$table_data .= '<td ><span><a class="more-info" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');"><i class="fa fa-search" aria-hidden="true"></i></a></span><span><a class="more-info" onclick=""></span></td>';
				$table_data .= '</tr>';  
			}
			else if ($attrs['status'] == "All" || $attrs['status'] == "all" ){
				
				// gf_apply_filters( 'gform_paypal_request', $data->form_id, "http://onlinemarketing.guru/booking/", $form, GFAPI::get_entry( $data->id ), paypal_get_feeds( $data->form_id),$submission_data ) 
				$get_setting = get_option('my_option_name') ;
			//	print_r($get_setting);
			//	$payment = $data->payment_method!=""?$data->payment_method:'';
		//	$payment = $data->payment_method!=""?$data->payment_method:"<a href = '".gf_apply_filters( 'gform_paypal_request', $data->form_id, "http://onlinemarketing.guru/booking/", $form, GFAPI::get_entry( $data->id ), paypal_get_feeds( $data->form_id),$submission_data ) ."'>Pay Now</a>";
	
$payment = $data->payment_method!=""?$data->payment_method:'<a class="more-info paynow" style="cursor: pointer;" onclick = "redirect('."'http://onlinemarketing.guru/booking/'".','. $data->id .','. $data->form_id.',\''. $get_email.'\');">Pay Now</a>';


			$table_data .= '<tr>';  
				$table_data .= '<td class="title" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');"><span><a >'.$data->id.'</a></span></td>'; 
				$table_data .= '<td ><span>'.$form_title.'</span></td>';
				//$table_data .= '<td ><span>'.$author_obj->user_login.'</span></td>';
				$table_data .= '<td ><span>'.$get_email.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_status.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_date.'</span></td>';
				//$table_data .= '<td ><span>'.$data->transaction_id.'</span></td>';
				$table_data .= '<td ><span>'.$value_recursive.'</span></td>';
				$table_data .= '<td ><span>'.$payment.'</span></td>';
				$table_data .= '<td ><span><a class="more-info" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');"><i class="fa fa-search" aria-hidden="true"></i></a></span><span></span></td>';
				$table_data .= '</tr>';  
			}
			else
			{ 
				// gf_apply_filters( 'gform_paypal_request', $data->form_id, "http://onlinemarketing.guru/booking/", $form, GFAPI::get_entry( $data->id ), get_feeds( $data->form_id),"" ) . "<br>";
			/* 	$get_setting = get_option('my_option_name') ;
 
				$payment = $data->payment_method!=""?$data->payment_method:'';
				//$payment = $data->payment_method!=""?$data->payment_method:apply_filters( 'gform_submit_button');
				$table_data .= '<tr>';  
			//	$table_data .= '<td class="title" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');"><span><a >#'.$data->id.'</a></span></td>'; 
				$table_data .= '<td ><span>'.$form_title.'</span></td>';
				//$table_data .= '<td ><span>'.$author_obj->user_login.'</span></td>';
				$table_data .= '<td ><span>'.$get_email.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_status.'</span></td>';
				$table_data .= '<td ><span>'.$data->payment_date.'</span></td>';
				//$table_data .= '<td ><span>'.$data->transaction_id.'</span></td>';
				$table_data .= '<td ><span>'.$value_recursive.'</span></td>';
				$table_data .= '<td ><span>'.$payment.'</span></td>'; 
				$table_data .= '<td ><span><a class="more-info" onclick="show_more_detail('.$title_display.','.$data->id.','.$data->form_id.',\''.$form_title.'\');">View</a></span></td>';
				$table_data .= '</tr>';   */
			}
			}
			}
	  }
	}
$table_data .="</tbody></table></div>"; 
	 $table_data .= apply_filters( 'hook_Modal', "" );
	 $table_data .= apply_filters( 'hook_loading_paypal', "" );
	 
  return $table_data;

}
add_shortcode('gravity_table', 'table_creation');



function theme_prefix_enqueue_script() {
	require_once( GFCommon::get_base_path() . '/currency.php' );
	 $currency = new RGCurrency( GFCommon::get_currency() );
	
   wp_add_inline_script( 'jquery-migrate', 'var gf_global = '.json_encode((array)$currency) );
}


/**
 * Show custom user profile fields
 * @param  obj $user The user object.
 * @return void
 */
function gravity_account($user){
	
	
	
	  global $current_user;
	  
	 
	  $current_url=strtok($_SERVER["REQUEST_URI"],'?'); 
	 
	$single = true;
	add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );
	 $shortcode_output .=" <h5 id='welcome'>Welcome back ".$current_user->user_login."!</h5>";
	$shortcode_output .= '<div class="title-wrap"><h2 >Account Details</h2 ></div>';
	

	 if(isset( $_SESSION['gtv_password']) ){
			$user_data = array(
					'ID' =>$current_user->ID,
					'user_pass' => $_SESSION['gtv_password']
				);
	//
	 $wrong_current_pass= "";
	 $email_exist= "";
	 $user = get_user_by( 'login', $current_user->user_login );
	 
		if (wp_check_password($_SESSION['gtv_current_password'], $user->data->user_pass, $current_user->ID  ) ) {
			wp_update_user(  $user_data );
		} else {
			$wrong_current_pass =  "&old_pass=wrong";
		}
	//  $updated = isset($_GET['email']) ?"":)  ;
	 if(isset($_GET['email']) ){
		 
		$email_exist= "&email=exist";
	 }
	 
	  unset($_SESSION['gtv_password']);
	  unset($_SESSION['gtv_current_password']);
		wp_redirect( $current_url."?updated=true".$email_exist.$wrong_current_pass);
		exit;
	}  
		// Finally, destroy the session.

	 // echo esc_attr( get_the_author_meta( 'gtv_coutry', $current_user->ID ) );

	 if ( isset($_GET['old_pass']) == "wrong"){
		 $shortcode_output .= '<div class="alert alert-danger">
			<strong>Error saving password!</strong><a class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
		  </div>';
	}

	if ( isset($_GET['email']) == 'exist'){
			$shortcode_output .= '<div class="alert alert-danger">
			<strong>Error saving email address!</strong><a class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
		  </div>';
		  	 unset($_GET['email']);
		  	 unset($_POST['action']);
	}
	else if ( isset($_GET['updated']) == 'true'){
			$shortcode_output .= ' <div class="alert alert-success">
			<strong>Update Successful!</strong><a class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
		  </div>';
	 unset($_POST['action']);
	}
	else{}
	$shortcode_output .= '<form method="post" id="adduser" action="">';
	$shortcode_output .= '<div class="wrap-form">';
    $shortcode_output .= '<label for="gtv_first_name">Name</label><div id="two_field_separated" style="margin-bottom: 22px;"><div id="first_field"> <input type="text" name="gtv_first_name" id="gtv_first_name" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_first_name', $single ) ).'" class="regular-text" /><label>First Name</label></div>';
    $shortcode_output .= '<div id="second_field"><input type="text" name="gtv_last_name" id="gtv_last_name" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_last_name', $single ) ) .'" class="regular-text" /><label >Last Name</label></div></div> ';
    $shortcode_output .= '<label for="phone_number">Phone Number</label><input type="text" name="phone_number" id="phone_number" value="'.  esc_attr(get_user_meta( $current_user->ID , 'phone_number', $single ) ) .'" class="regular-text" />';
 
    $shortcode_output .= '<label for="gtv_email">Email Address</label><input type="email" name="gtv_email" id="gtv_email" value="'. $current_user->user_email .'" class="regular-text" />';
  
     $shortcode_output .= '<label for="gtv_current_password">Old Password</label><input type="password" placeholder="Old Password" name="gtv_current_password" id="gtv_current_password" value="" class="regular-text" />';
	
   $shortcode_output .= '<label for="gtv_password">Password</label><div id="two_field_separated" style="margin-bottom: 22px;"><div id="first_field"> <input class="regular-text form-control " value=""  id="gtv_password" name="gtv_password" type="password" data-minlength="6" placeholder="Password"><label>Password</label></div>';
    $shortcode_output .= '<div id="second_field"><input data-match="#gtv_password" type="password" name="gtv_confirm_password" id="inputPasswordConfirm" value="" class="regular-text form-control " placeholder="Confirm Password"><label >Confirm Password</label></div></div> ';
   
    $shortcode_output .= '<label for="gtv_websites">Website</label><input type="text" name="gtv_websites" id="gtv_websites" value="'.  esc_attr( get_the_author_meta( "gtv_websites", $current_user->ID ) ) .'" class="regular-text" />';
  
	$shortcode_output .= '<tbody></table></div>';
	
	$shortcode_output .= '<div class="title-wrap"><h2 >Billing Information</h2 ></div>';
	$shortcode_output .= '<div class="wrap-form">';
   $shortcode_output .= '<div id="two_field_separated" ><div id="first_field"><label class="label-head">
Company Name</label><input type="text" name="gtv_company_name" id="gtv_company_name" value="'. esc_attr( get_user_meta( $current_user->ID , 'gtv_company_name', $single ) )  .'" class="regular-text" /></div>';
   $shortcode_output .= '<div id="second_field"><label class="label-head">Building Name or Number</label><input type="text" name="gtv_building_name" id="gtv_building_name" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_building_name', $single ) )  .'" class="regular-text" /></div></div>';
   
   $shortcode_output .= '<label for="gtv_addres_line_one">Address Line 1</label> <td><input type="text" name="gtv_addres_line_one" id="gtv_addres_line_one" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_addres_line_one', $single ) ) .'" class="regular-text" />';
   
   $shortcode_output .= '<label for="gtv_addres_line_two">Address Line 2</label><input type="text" name="gtv_addres_line_two" id="gtv_addres_line_two" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_addres_line_two', $single ) ) .'" class="regular-text" />';
   
   $shortcode_output .= '<div id="two_field_separated"><div id="first_field"><label class="label-head">City / State</label><input type="text" name="gtv_city_state" id="gtv_city_state" value="'.  esc_attr( get_user_meta( $current_user->ID , 'gtv_city_state', $single ) )  .'" class="regular-text" /></div>';
   
   $shortcode_output .= '<div id="second_field"><label class="label-head">Postcode / Zipcode</label><input type="text" name="gtv_postcode_zipcode" id="gtv_postcode_zipcode" value="'.  esc_attr( get_the_author_meta( "gtv_postcode_zipcode", $current_user->ID ) ) .'" class="regular-text" /></div></div>';
   
  // $shortcode_output .= '<tr><th><label for="gtv_coutry">Country</label></th> <td><select name="gtv_coutry">'.country_select().'</select></td></tr>';
     $shortcode_output .=  '<label for="billing_country_front"> Billing Country</label><select name="billing_country_front">'.country_select().'</select>'; 
 	$shortcode_output .= '';
	 do_action('edit_user_profile',$user); 
	$shortcode_output .= '<p class="form-submit">  <input name="updateuser" type="submit" id="updateuser" class="submit button" value="Update" />'.wp_nonce_field( 'update-user_'. $current_user->ID ).' <input name="action" type="hidden" id="action" value="update-user" /><input name="user_id" type="hidden" id="g_user_id" value=""/></p>';
 	$shortcode_output .= '</div></form></div><script>jQuery(document).ready(function() { jQuery("select[name=billing_country_front]").val("'.get_the_author_meta( 'gtv_billing_country', $current_user->ID ).'"); jQuery("select[name=gtv_coutry]").val("'.get_the_author_meta( 'gtv_coutry', $current_user->ID ).'");jQuery("#g_user_id").val("'.$current_user->ID.'");});</script>';
 	$shortcode_output .= '<div id="wrap-notify"><div id="notify" class="open animate"><div id="notify-body" class="container"><div id="notify-content"></div> <div id="notify-close"><i class="fa fa-angle-double-down"></i> </div></div></div></div>';
	if(!is_admin()){
		add_action( 'personal_options_update', 'gravity_account');
		add_action( 'edit_user_profile_update', 'gravity_account');
	 }
  return $shortcode_output;

}
add_shortcode('gravity_account', 'gravity_account');




 function country_select() {
		return "<option value='Afghanistan'>Afghanistan</option>
<option value='Albania'>Albania</option>
<option value='Algeria'>Algeria</option>
<option value='American Samoa'>American Samoa</option>
<option value='Andorra'>Andorra</option>
<option value='Angola'>Angola</option>
<option value='Antigua and Barbuda'>Antigua and Barbuda</option>
<option value='Argentina'>Argentina</option>
<option value='Armenia'>Armenia</option>
<option value='Australia'>Australia</option>
<option value='Austria'>Austria</option>
<option value='Azerbaijan'>Azerbaijan</option>
<option value='Bahamas'>Bahamas</option>
<option value='Bahrain'>Bahrain</option>
<option value='Bangladesh'>Bangladesh</option>
<option value='Barbados'>Barbados</option>
<option value='Belarus'>Belarus</option>
<option value='Belgium'>Belgium</option>
<option value='Belize'>Belize</option>
<option value='Benin'>Benin</option>
<option value='Bermuda'>Bermuda</option>
<option value='Bhutan'>Bhutan</option>
<option value='Bolivia'>Bolivia</option>
<option value='Bosnia and Herzegovina'>Bosnia and Herzegovina</option>
<option value='Botswana'>Botswana</option>
<option value='Brazil'>Brazil</option>
<option value='Brunei'>Brunei</option>
<option value='Bulgaria'>Bulgaria</option>
<option value='Burkina Faso'>Burkina Faso</option>
<option value='Burundi'>Burundi</option>
<option value='Cambodia'>Cambodia</option>
<option value='Cameroon'>Cameroon</option>
<option value='Canada'>Canada</option>
<option value='Cape Verde'>Cape Verde</option>
<option value='Cayman Islands'>Cayman Islands</option>
<option value='Central African Republic'>Central African Republic</option>
<option value='Chad'>Chad</option>
<option value='Chile'>Chile</option>
<option value='China'>China</option>
<option value='Colombia'>Colombia</option>
<option value='Comoros'>Comoros</option>
<option value='Congo, Democratic Republic of the'>Congo, Democratic Republic of the</option>
<option value='Congo, Republic of the'>Congo, Republic of the</option>
<option value='Costa Rica'>Costa Rica</option>
<option value='Côte d'Ivoire'>Côte d'Ivoire</option>
<option value='Croatia'>Croatia</option>
<option value='Cuba'>Cuba</option>
<option value='Curaçao'>Curaçao</option>
<option value='Cyprus'>Cyprus</option>
<option value='Czech Republic'>Czech Republic</option>
<option value='Denmark'>Denmark</option>
<option value='Djibouti'>Djibouti</option>
<option value='Dominica'>Dominica</option>
<option value='Dominican Republic'>Dominican Republic</option>
<option value='East Timor'>East Timor</option>
<option value='Ecuador'>Ecuador</option>
<option value='Egypt'>Egypt</option>
<option value='El Salvador'>El Salvador</option>
<option value='Equatorial Guinea'>Equatorial Guinea</option>
<option value='Eritrea'>Eritrea</option>
<option value='Estonia'>Estonia</option>
<option value='Ethiopia'>Ethiopia</option>
<option value='Faroe Islands'>Faroe Islands</option>
<option value='Fiji'>Fiji</option>
<option value='Finland'>Finland</option>
<option value='France'>France</option>
<option value='French Polynesia'>French Polynesia</option>
<option value='Gabon'>Gabon</option>
<option value='Gambia'>Gambia</option>
<option value='Georgia'>Georgia</option>
<option value='Germany'>Germany</option>
<option value='Ghana'>Ghana</option>
<option value='Greece'>Greece</option>
<option value='Greenland'>Greenland</option>
<option value='Grenada'>Grenada</option>
<option value='Guam'>Guam</option>
<option value='Guatemala'>Guatemala</option>
<option value='Guinea'>Guinea</option>
<option value='Guinea-Bissau'>Guinea-Bissau</option>
<option value='Guyana'>Guyana</option>
<option value='Haiti'>Haiti</option>
<option value='Honduras'>Honduras</option>
<option value='Hong Kong'>Hong Kong</option>
<option value='Hungary'>Hungary</option>
<option value='Iceland'>Iceland</option>
<option value='India'>India</option>
<option value='Indonesia'>Indonesia</option>
<option value='Iran'>Iran</option>
<option value='Iraq'>Iraq</option>
<option value='Ireland'>Ireland</option>
<option value='Israel'>Israel</option>
<option value='Italy'>Italy</option>
<option value='Jamaica'>Jamaica</option>
<option value='Japan'>Japan</option>
<option value='Jordan'>Jordan</option>
<option value='Kazakhstan'>Kazakhstan</option>
<option value='Kenya'>Kenya</option>
<option value='Kiribati'>Kiribati</option>
<option value='North Korea'>North Korea</option>
<option value='South Korea'>South Korea</option>
<option value='Kosovo'>Kosovo</option>
<option value='Kuwait'>Kuwait</option>
<option value='Kyrgyzstan'>Kyrgyzstan</option>
<option value='Laos'>Laos</option>
<option value='Latvia'>Latvia</option>
<option value='Lebanon'>Lebanon</option>
<option value='Lesotho'>Lesotho</option>
<option value='Liberia'>Liberia</option>
<option value='Libya'>Libya</option>
<option value='Liechtenstein'>Liechtenstein</option>
<option value='Lithuania'>Lithuania</option>
<option value='Luxembourg'>Luxembourg</option>
<option value='Macedonia'>Macedonia</option>
<option value='Madagascar'>Madagascar</option>
<option value='Malawi'>Malawi</option>
<option value='Malaysia'>Malaysia</option>
<option value='Maldives'>Maldives</option>
<option value='Mali'>Mali</option>
<option value='Malta'>Malta</option>
<option value='Marshall Islands'>Marshall Islands</option>
<option value='Mauritania'>Mauritania</option>
<option value='Mauritius'>Mauritius</option>
<option value='Mexico'>Mexico</option>
<option value='Micronesia'>Micronesia</option>
<option value='Moldova'>Moldova</option>
<option value='Monaco'>Monaco</option>
<option value='Mongolia'>Mongolia</option>
<option value='Montenegro'>Montenegro</option>
<option value='Morocco'>Morocco</option>
<option value='Mozambique'>Mozambique</option>
<option value='Myanmar'>Myanmar</option>
<option value='Namibia'>Namibia</option>
<option value='Nauru'>Nauru</option>
<option value='Nepal'>Nepal</option>
<option value='Netherlands'>Netherlands</option>
<option value='New Zealand'>New Zealand</option>
<option value='Nicaragua'>Nicaragua</option>
<option value='Niger'>Niger</option>
<option value='Nigeria'>Nigeria</option>
<option value='Northern Mariana Islands'>Northern Mariana Islands</option>
<option value='Norway'>Norway</option>
<option value='Oman'>Oman</option>
<option value='Pakistan'>Pakistan</option>
<option value='Palau'>Palau</option>
<option value='Palestine, State of'>Palestine, State of</option>
<option value='Panama'>Panama</option>
<option value='Papua New Guinea'>Papua New Guinea</option>
<option value='Paraguay'>Paraguay</option>
<option value='Peru'>Peru</option>
<option value='Philippines'>Philippines</option>
<option value='Poland'>Poland</option>
<option value='Portugal'>Portugal</option>
<option value='Puerto Rico'>Puerto Rico</option>
<option value='Qatar'>Qatar</option>
<option value='Romania'>Romania</option>
<option value='Russia'>Russia</option>
<option value='Rwanda'>Rwanda</option>
<option value='Saint Kitts and Nevis'>Saint Kitts and Nevis</option>
<option value='Saint Lucia'>Saint Lucia</option>
<option value='Saint Vincent and the Grenadines'>Saint Vincent and the Grenadines</option>
<option value='Samoa'>Samoa</option>
<option value='San Marino'>San Marino</option>
<option value='Sao Tome and Principe'>Sao Tome and Principe</option>
<option value='Saudi Arabia'>Saudi Arabia</option>
<option value='Senegal'>Senegal</option>
<option value='Serbia'>Serbia</option>
<option value='Seychelles'>Seychelles</option>
<option value='Sierra Leone'>Sierra Leone</option>
<option value='Singapore'>Singapore</option>
<option value='Sint Maarten'>Sint Maarten</option>
<option value='Slovakia'>Slovakia</option>
<option value='Slovenia'>Slovenia</option>
<option value='Solomon Islands'>Solomon Islands</option>
<option value='Somalia'>Somalia</option>
<option value='South Africa'>South Africa</option>
<option value='Spain'>Spain</option>
<option value='Sri Lanka'>Sri Lanka</option>
<option value='Sudan'>Sudan</option>
<option value='Sudan, South'>Sudan, South</option>
<option value='Suriname'>Suriname</option>
<option value='Swaziland'>Swaziland</option>
<option value='Sweden'>Sweden</option>
<option value='Switzerland'>Switzerland</option>
<option value='Syria'>Syria</option>
<option value='Taiwan'>Taiwan</option>
<option value='Tajikistan'>Tajikistan</option>
<option value='Tanzania'>Tanzania</option>
<option value='Thailand'>Thailand</option>
<option value='Togo'>Togo</option>
<option value='Tonga'>Tonga</option>
<option value='Trinidad and Tobago'>Trinidad and Tobago</option>
<option value='Tunisia'>Tunisia</option>
<option value='Turkey'>Turkey</option>
<option value='Turkmenistan'>Turkmenistan</option>
<option value='Tuvalu'>Tuvalu</option>
<option value='Uganda'>Uganda</option>
<option value='Ukraine'>Ukraine</option>
<option value='United Arab Emirates'>United Arab Emirates</option>
<option value='United Kingdom'>United Kingdom</option>
<option value='United States'>United States</option>
<option value='Uruguay'>Uruguay</option>
<option value='Uzbekistan'>Uzbekistan</option>
<option value='Vanuatu'>Vanuatu</option>
<option value='Vatican City'>Vatican City</option>
<option value='Venezuela'>Venezuela</option>
<option value='Vietnam'>Vietnam</option>
<option value='Virgin Islands, British'>Virgin Islands, British</option>
<option value='Virgin Islands, U.S.'>Virgin Islands, U.S.</option>
<option value='Yemen'>Yemen</option>
<option value='Zambia'>Zambia</option>
<option value='Zimbabwe'>Zimbabwe</option>";
	}
 function paypal_get_feeds( $form_id = null ) {
		global $wpdb;

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}gf_addon_feed
                               WHERE addon_slug=%s {$form_filter} ORDER BY 'feed_order', 'id' ASC"	,"gravityformspaypal");

		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $results as &$result ) {
			$result['meta'] = json_decode( $result['meta'], true );
		}
		return $results;
	}
	
function update_url( $url, $form, $entry,$feed,$submission_data ) {
    //parse url into its individual pieces (host, path, querystring, etc.)
	 $production_url = 'https://www.paypal.com/cgi-bin/webscr/';
	 $sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr/';
   	if ( ! rgempty( 'gf_paypal_return', $_GET ) ) {
			return false;
		}
		$url = $feed['meta']['mode'] == 'production' ? $production_url : $sandbox_url;
		$invoice_id = apply_filters( 'gform_paypal_invoice', '', $form, $entry );
			$invoice = empty( $invoice_id ) ? '' : "&invoice={$invoice_id}";
				$currency = rgar( $entry, 'currency' );
				$customer_fields = customer_get_query_string( $feed, $entry );
		$page_style = ! empty( $feed['0']['meta']['pageStyle'] ) ? '&page_style=' . urlencode( $feed['0']['meta']['pageStyle'] ) : '';

		//Continue link text
		$continue_text = ! empty( $feed['0']['meta']['continueText'] ) ? '&cbt=' . urlencode( $feed['0']['meta']['continueText'] ) : '&cbt=' . __( 'Click here to continue', 'gravityformspaypal' );
		
		$return_mode = '2';
		
		$return_url = '&return=' . urlencode( get_return_url( $form['id'], $entry['id'] ) ) . "&rm={$return_mode}"; 
		
		$cancel_url = ! empty( $feed['0']['meta']['cancelUrl'] ) ? '&cancel_return=' . urlencode( $feed['0']['meta']['cancelUrl'] ) : '';
	
		//Don't display note section
		$disable_note = ! empty( $feed['0']['meta']['disableNote'] ) ? '&no_note=1' : '';
			
		//Don't display shipping section
		$disable_shipping = ! empty( $feed['0']['meta']['disableShipping'] ) ? '&no_shipping=1' : '';

		//URL that will listen to notifications from PayPal
		$ipn_url = urlencode( get_bloginfo( 'url' ) . '/?page=gf_paypal_ipn' );	
			
		$business_email = urlencode( trim( $feed['0']['meta']['paypalEmail'] ) );
		//echo $business_email;
		$custom_field   = $entry['id'] . '|' . wp_hash( $entry['id'] );
			$url .= "?notify_url={$ipn_url}&charset=UTF-8&currency_code={$currency}&business={$business_email}&custom={$custom_field}{$invoice}{$customer_fields}{$page_style}{$continue_text}{$cancel_url}{$disable_note}{$disable_shipping}{$return_url}";
		$query_string = '';
/* echo "<pre>";
			print_r( $submission_data  );
echo "</pre>";  */
// echo $feed['meta']['transactionType'];
			switch ( $feed['0']['meta']['transactionType'] ) {
			case 'product' :
				//build query string using $submission_data
			$query_string = GFPayPal::get_product_query_string( $submission_data, $entry['id'] );
		
	// echo "adsfasdfasdfs";
				break;
			case 'donation' :
				$query_string = GFPayPal::get_donation_query_string( $submission_data, $entry['id'] );
					
				break;

			case 'subscription' :
				$query_string = GFPayPal::get_subscription_query_string( $feed, $submission_data, $entry['id'] );
			
				break;
		}
$query_string = gf_apply_filters( 'gform_paypal_query', $form['id'], $query_string, $form, $entry, $feed, $submission_data );  

		if ( ! $query_string ) {
			//$this->log_debug( __METHOD__ . '(): NOT sending to PayPal: The price is either zero or the gform_paypal_query filter was used to remove the querystring that is sent to PayPal.' );

			return '';
		}

		$url .= $query_string;

		//$url = gf_apply_filters( 'gform_paypal_request', $form['id'], $url, $form, $entry, $feed, $submission_data );
		
		//add the bn code (build notation code)
		$url .= '&bn=Rocketgenius_SP'; 

	//	log_debug( __METHOD__ . "(): Sending to PayPal: {$url}" );
		
		//echo $url ;
		//updating lead's payment_status to Processing
	//	GFAPI::update_entry_property( $entry['id'], 'payment_status', 'Processing' );

    return $url;
}


 function get_submission_data( $feed, $form, $entry ) 
{

		
}
function get_return_url( $form_id, $lead_id ) {
		//$pageURL = GFCommon::is_ssl() ? 'https://' : 'http://';

		$server_port = apply_filters( 'gform_paypal_return_url_port', $_SERVER['SERVER_PORT'] );

		if ( $server_port != '80' ) {
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $server_port . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .=  $_POST['url'];
		}

		$ids_query = "ids={$form_id}|{$lead_id}";
		$ids_query .= '&hash=' . wp_hash( $ids_query );
		$url = add_query_arg( 'gf_paypal_return', base64_encode( $ids_query ), $pageURL );

		$query = 'gf_paypal_return=' . base64_encode( $ids_query );
		/**
		 * Filters PayPal's return URL, which is the URL that users will be sent to after completing the payment on PayPal's site.
		 * Useful when URL isn't created correctly (could happen on some server configurations using PROXY servers).
		 *
		 * @since 2.4.5
		 *
		 * @param string  $url 	The URL to be filtered.
		 * @param int $form_id	The ID of the form being submitted.
		 * @param int $entry_id	The ID of the entry that was just created.
		 * @param string $query	The query string portion of the URL.
		 */
		
		return apply_filters( 'gform_paypal_return_url', $url, $form_id, $lead_id, $query  );

	}


 function customer_get_query_string( $feed, $entry ) {
		$fields = '';

		foreach ( GFPayPal::get_customer_fields() as $field ) {
			$field_id = $feed['meta'][ $field['meta_name'] ];
			$value    = rgar( $entry, $field_id );

			if ( $field['name'] == 'country' ) {
				$value = class_exists( 'GF_Field_Address' ) ? GF_Fields::get( 'address' )->get_country_code( $value ) : GFCommon::get_country_code( $value );
			} elseif ( $field['name'] == 'state' ) {
				$value = class_exists( 'GF_Field_Address' ) ? GF_Fields::get( 'address' )->get_us_state_code( $value ) : GFCommon::get_us_state_code( $value );
			}

			if ( ! empty( $value ) ) {
				$fields .= "&{$field['name']}=" . urlencode( $value );
			}
		
		}

		return $fields;
	}


add_filter( 'gform_paypal_query', 'update_paypal_query', 10, 3 );
function update_paypal_query( $query_string, $form, $entry ) {

    parse_str( $query_string, $query );
    
    $id = 0;
    $amounts = array();
    
    foreach ( $query as $key => $value ) {
        
        if ( (int) $value >= 0 ) {
            $amounts[] = $value;
            continue;
        } else {
            $id = str_replace( 'amount_', '*', $key );
            $discount = abs( $value );   
        }
        
    }
    
    if ( $id ) {
        unset( $query['item_name_' . $id] );
        unset( $query['amount_' . $id] );
        unset( $query['quantity_' . $id] );
    }
    
    foreach ( $query as $key => &$value ) {
        if ( strpos( $key, 'amount_' ) !== false ) {
            $value = $value - $discount;
        }
    }
    
    $query_string = http_build_query( $query, '', '&' );
    
    return '&' . $query_string;
} 

// filter the Gravity Forms button type

function value_to_money( $value  ) {
	require_once( GFCommon::get_base_path() . '/currency.php' );
     $currency = new RGCurrency( GFCommon::get_currency() );
      $money = $currency->to_money( $value );
    return $money;
}
add_filter( 'hook_RGCurrency', 'value_to_money', 10, 1 );

function array_filter_recursive( $array , $callback = null ) {
    foreach ($array as $key => & $value) {
        if (is_array($value)) {
            $value = (object)array_filter_recursive($value, $callback);
        }
        else {
            if ( ! is_null($callback)) {
                if ( ! $callback($value)) {
                    unset($array[$key]);
                }
            }
            else {
                if ( ! (bool) $value) {
                    unset($array[$key]);
                }
            }
        }
    }
    unset($value);
    return $array;
}
add_filter( 'hook_filter_recursive', 'array_filter_recursive', 10, 2 );

function get_the_form_title($form_id) {
  $forminfo = RGFormsModel::get_form($form_id);
  $form_title = $forminfo->title;
  return $form_title;
}
add_filter( 'hook_get_the_form_title', 'get_the_form_title', 10, 1);

function Sortbydate($a, $b)
 {
   return $b->id - $a->id;
 }
 
add_filter( 'hook_Modal', 'post_love_display', 99 );
function post_love_display() {
	    $love_text = '<div id="myModal" class="'. esc_attr( "modal" ).'">';
		$love_text .= '<div class="modal-content"> <div class="header-modal"><span class="header-title"></span><span ><a class="close">x</a></span></div><div id="details"></div></div>';
		$love_text .= '</div>';
	return  $love_text;

}
add_filter( 'hook_loading_paypal', 'loading_paypal', 99 );
function loading_paypal() {
	    $loading_paypal = '<div class="block_content"><div style="width: 90px;height: 90px;margin: 22% auto;" class="loader"></div></div>';
		/* $loading_paypal .= '<span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>';
		$loading_paypal .= '</div>'; */
	return  $loading_paypal;
}


add_action( 'wp_ajax_nopriv_post_love_add_love', 'post_love_add_love' );
add_action( 'wp_ajax_post_love_add_love', 'post_love_add_love' );


function get_fields($form_id,$field_id)
{
	$form = RGFormsModel::get_form_meta($form_id);
	
	 $fields = $form['fields'];
    $form_fields = array(); 
    foreach($fields as $field)
    {
        $form_fields[$field['id']] = $field['label'];
    }
	return $form_fields[$field_id];
}

function post_love_add_love() {
	header("Content-Type: application/json", true);
	$love = $_POST['post_id'];
	$form_id = $_POST['form_id'];
	$entry = array_filter(GFAPI::get_entry( $love ));
    $referrer = gform_get_meta( $love, 'id' );
	
	
	/* $form_fields['value']['repeater'] = 'null';
	$form_fields['value']['Repeater'] = 'null'; */
	
	$form = RGFormsModel::get_form_meta($form_id);
	
	 $fields = $form['fields'];
    $form_fields = array(); 
    foreach($fields as $field)
    {
        $form_fields[$field['id']] = $field['label'];
    }
	//echo $form_fields[128];
	$count = 0;
	foreach($entry as $key=>$value)
	{
		$data = @unserialize($value);
		if ($data !== false) 
		{
			$entry[$key] = unserialize($value);
			//$entry[$key] = "";
			//print_r($entry[$key]);
			 foreach($entry[$key] as $field=>$field_1value)
			{
				
					foreach($field_1value as $field_1=>$field_1val)
					{
						 foreach($field_1val as $keyd=>$valued)
						 {
							/*  echo count($entry[$key]);
							 echo "<pre>";
							 /*  print_r(  $entry[$key][$count++]);
							 echo "</pre>"; 
							   // $entry[$key][$count++] = 
							   print_r( change_key( $entry[$key][1], $field_1, $form_fields[$field_1]));
							  // echo $count++;
							 echo "</pre>"; */
							//print_r($entry[$key][$field][$field_1] ); 
							 $entry[$key][$field][$field_1] = array($form_fields[$field_1] => $valued);
						   
						 }
						
							// unset( $entry[$key] );
					}
					
			} 
			//echo "ok".'<br>';
		} 
		else
		{
			//echo "not ok".'<br>';
		}
	}
	
	$form_fields = get_all_form_fields($form_id, $entry,$form_fields );
	
	/* $form = RGFormsModel::get_form_meta($form_id);
	
	 $fields = $form['fields'];
    $form_fields = array(); 
    foreach($fields as $field)
    {
        $form_fields[$field['id']] = $field['label'];
    }
	echo $form_fields[130]; */
	//    echo "<pre>";
	 //print_r($form_fields); 
	
	// sort($form_fields, SORT_NUMERIC);
	echo json_encode($form_fields);
	// echo "<pre>"; 
	die();
}
function get_all_form_fields($form_id,$entry,$form_fields)
	{
        $form = RGFormsModel::get_form_meta($form_id);
		$repeater = array();
        $fields = array();
		$fiele_value = $entry;
		$count = 0;
        if(is_array($form["fields"]))
		{
			
             foreach($form["fields"] as $field){
                if(isset($field["inputs"]) && is_array($field["inputs"]))
				{
                     foreach($field["inputs"] as $input)
						$fields[$input["id"]] = GFCommon::get_label($field, $input["id"]); 
                }
                else if(!rgar($field, 'displayOnly'))
				{
						$fields[$field["id"] ] = GFCommon::get_label($field);
				}	
			}
			
		}
		//print_r($fiele_value);
		foreach($entry as $key=>$value)
		{
			foreach($fields as $key_fields=>$value_fields)
			{
				if(is_array($value))
				{
				$fiele_value = change_key( $fiele_value, $key,  " Repeater#".$key. "/~/(".$form_fields[$key]. ")");
					//echo "<script>console.log('".$key." = ".$form_fields[$key]."')</script>";
				}
				else if($key == $key_fields)
				{
					/* if($key_fields == "Repeater")
					{
					echo "<script>alert(". $key_fields.") </script>";
					} */
				
					$fiele_value = change_key( $fiele_value, $key, $value_fields);
				}
				else
				{
					/* if($key == '50')
					{ */
						
					//	$fiele_value = change_key( $fiele_value, $key, $key.' '. "Titled ~ ".$form_fields[$key]);
					// }
				}
			}
			
		}
        return array( "Fields" => $fiele_value )  ;
	}
function change_key( $array, $old_key, $new_key)
{
    if( ! array_key_exists( $old_key, $array ) )
        return $array;

    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;

    return array_combine( $keys, $array );
}
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}
add_filter( 'plugin_row_meta', 'gravity_view_meta_links', 10, 2 );
function gravity_view_meta_links( $links, $file ) {

	$plugin = plugin_basename(__FILE__);

// create the links
	/* if ( $file == $plugin ) {

		$supportlink = 'https://wordpress.org/support/plugin/tabby-responsive-tabs';
		$donatelink = 'http://cubecolour.co.uk/wp';
		$reviewlink = 'https://wordpress.org/support/view/plugin-reviews/tabby-responsive-tabs?rate=5#postform';
		$twitterlink = 'http://twitter.com/cubecolour';
		$customiselink = 'http://cubecolour.co.uk/tabby-responsive-tabs-customiser';
		$iconstyle = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;"';

		if ( is_plugin_active( 'tabby-responsive-tabs-customiser/tabby-customiser.php' ) ) {
			$customiselink = admin_url( 'options-general.php?page=tabby-settings' );
		}

		return array_merge( $links, array(
			'<a href="' . $supportlink . '"> <span class="dashicons dashicons-lightbulb" ' . $iconstyle . 'title="Tabby Responsive Tabs Support"></span></a>',
			'<a href="' . $twitterlink . '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="Cubecolour on Twitter"></span></a>',
			'<a href="' . $reviewlink . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="Give a 5 Star Review"></span></a>',
			'<a href="' . $donatelink . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="Donate"></span></a>',
			'<a href="' . $customiselink . '"><span class="dashicons dashicons-admin-appearance" ' . $iconstyle . 'title="Tabby Responsive Tabs Customizer"></span></a>'
		) );
	} */

	return $links;
}

add_action( 'wp_ajax_nopriv_url_payment', 'url_payment' );
add_action( 'wp_ajax_url_payment', 'url_payment' );

function url_payment()
{
		/* echo ;form_id
		get_entry */
			global $current_user;
		
		$form =GFAPI::get_form( $_POST['form_id'] );
		$gform_product_info__ = gform_get_meta( $_POST['get_entry'], 'gform_product_info__' );
		$get_amount = gform_get_meta( $_POST['get_entry'], 'payment_amount' );
		$submission_data =Array(
		"form_title" =>$form['title'],
		"email"=>$_POST['email'],
		"address" =>"",
		"address2" => "",
		"city" =>"",
		"state" => "",
		"zip" => "",
		"country"=>"" /* et_the_author_meta( "gtv_coutry", $current_user_id) */,
		"payment_amount" =>  $get_amount,
		"setup_fee" => 0,
		"trial" => 0,
		"line_items" => Array ()
	 );

	$count = 0;
	foreach($gform_product_info__['products'] as $key => $value)
	{
		array_push($submission_data['line_items'], Array ());
		$submission_data['line_items'][$count]['id'] = $key;
		
		foreach( $value as $key_items => $key_value){
		if($key_items == "price")
		{
			$submission_data['line_items'][$count]["unit_price"] =preg_replace('/[^A-Za-z0-9\. -]/', '', $key_value) ;
		}else
		{
			$submission_data['line_items'][$count][$key_items] = $key_value ;
		}
			
		}
		$count ++;
	}
 

	add_filter( 'gform_paypal_request', 'update_url', 10, 5 );
	echo gf_apply_filters( 'gform_paypal_request', $_POST['form_id'], "http://onlinemarketing.guru/booking/", $form ,GFAPI::get_entry( $_POST['get_entry'] ), paypal_get_feeds( $_POST['form_id'] ),$submission_data ) ;

}


?>
