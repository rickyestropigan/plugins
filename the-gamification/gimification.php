<?php 
/* this developement is part adons for woocommerce plugins */
/* this is a general class to be use in any extended class */


class GIMIFICATION{
	
	public function __construct(){
		global $woocommerce;
		
		//Activation / Deactivation hooks
		register_activation_hook(__FILE__, array(__CLASS__, 'check_plugin_activated'));
		register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));
		
		//add_action('init',array(__CLASS__,'gimi_user_profile')); 
		add_action( 'init', array(__CLASS__,'gimi_item_profile' ));
		//add_action( 'init', array(&$this,'gami_add_point_fromvendor_activation' ));
		add_filter( 'woocommerce_account_menu_items', array(__CLASS__,'gimi_account_menu_items' ));
		add_action( 'woocommerce_account_gimification-profile_endpoint', array(__CLASS__,'gimi_myaccount_itemcontent') );
		add_action( 'woocommerce_thankyou', array(&$this,'gami_porduct_order_complete'), 1 );
		add_filter( 'user_contactmethods', array(&$this,'new_contact_methods'), 10, 1 );
		add_filter( 'manage_users_columns', array(&$this,'gami_user_point_column'));
		add_filter( 'manage_users_custom_column', array(&$this,'gami_user_point_column_content'), 10, 3 );
		
		add_filter('manage_gamification_posts_columns', array(&$this,'gami_point_column'));
		add_action('manage_posts_custom_column', array(&$this,'gami_piont_column_content'), 10, 2);


		
	}
	//general action for gami actions
	public function gami_actions($gami_values=null) {
		global $gimi_options, $badge_earn_by, $show_to_user, $woocommerce;
		
		$post_arr = array(
			'post_title'   => 'Gamification Points',
			'post_content' => 'Gamification Points acquired from buying products',
			'post_status'  => 'private',
			'post_type'  => 'gamification',
			'post_author'  => get_current_user_id()
		);
		
		$post_id = wp_insert_post( $post_arr );
		
		add_post_meta( $post_id, 'gami_product_items', $gami_values['qty'], true );
		add_post_meta( $post_id, 'gami_points', $gami_values['points_earn'], true );
		add_post_meta( $post_id, 'gami_action_type', $gami_values['from_post_type'], true ); //comment, post, commencts etc...
		add_post_meta( $post_id, 'gami_acquired_post_id', $gami_values['order_id'], true ); // from woocommerce order
		
	}
	
	public function gami_options(){
		global $gimi_options;
		$gimi_options = get_option('gimi_settings'); 
	}
	
	// ADD NEW COLUMN
	public function gami_point_column($columns) {
		
		unset(
			$columns['date'],
			$columns['title']
		);
		$new_columns = array(
			'title' => __('Acquired', 'Gamification'),
			'author' => __('User', 'Gamification'),
			'gami_points' => __('Points', 'Gamification'),
			'gami_points_from' => __('Event From', 'Gamification'),
			'date' => __('Date Acquired', 'Gamification'),
		);
		
		return array_merge($columns, $new_columns);

	}
	 
	
	public function gami_piont_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'gami_points':
				
				//print_r(get_post_meta($post_id));
				if ($_GET['post_type']=='gamification'){
				$gami_points = get_post_meta( $post_id, 'gami_points', true );
				echo $gami_points;
				}
				break;

			case 'gami_points_from':
				$gami_acquired_post_type = get_post_meta( $post_id, 'gami_acquired_post_type', true );
				echo $gami_acquired_post_type;
				break;
		}
	}

	public function new_contact_methods( $contactmethods ) {
		$contactmethods['gamipoints'] = 'Gami Points';
		return $contactmethods;
	}



	public function gami_user_point_column( $column ) {
		$column['gamipoints'] = 'Gami Points';
		return $column;
	}


	public function gami_user_point_column_content( $val, $column_name, $user_id ) {
		switch ($column_name) {
			case 'gamipoints' :
				return get_the_author_meta( 'gami_points', $user_id );
				
				break;
			default:
		}
		return $val;
	}

	function gami_post_columns($columns) {
		
		unset(
			$columns['title']
		);
		$new_columns = array(
			'title' => __('Publisher', 'ThemeName')
		);
		return array_merge($columns, $new_columns);
	}
	

	public function gami_add_point_fromvendor_activation( $order_id ) {
		$gami_user = self::gimi_user_profile();
		if ($gami_user['caps'] == "pending_vendor"){
			
		}
	}
	public function gami_porduct_order_complete( $order_id ) {
		//post new gamification post when woocommerce order complete
		global $woocommerce;
		global $gimi_user_data; 
		global $gimi_options;
		$qty  = 0;
		// Lets grab the order
		$order = wc_get_order( $order_id );
		
		// This is the order total
		$order->get_total();
	
		// This is how to grab line items from the order 
		$line_items = $order->get_items();
		
/* 		echo "<pre>";
		print_r($line_items);
		echo "</pre>"; */
		
		// This loops over line items
		foreach ( $line_items as $item ) {
			// This will be a product
			$product = $order->get_product_from_item( $item );
	  
			// This is the products SKU
			$sku = $product->get_sku();
			
			// This is the qty purchased
			$qty = $qty+$item['qty'];
			
			// Line item total cost including taxes and rounded
			$total = $order->get_line_total( $item, true, true );
			
			// Line item subtotal (before discounts)
			$subtotal = $order->get_line_subtotal( $item, true, true );
		}
		//echo $qty;
		
		$points_earn = $qty * $gimi_options['posts_points'];
		
		$post_arr = array(
			'post_title'   => 'Gamification Points',
			'post_content' => 'Gamification Points acquired from buying products',
			'post_status'  => 'private',
			'post_type'  => 'gamification',
			'post_author'  => get_current_user_id()
		);
		
		$post_id = wp_insert_post( $post_arr );
		
		add_post_meta( $post_id, 'gami_product_items', $qty, true );
		add_post_meta( $post_id, 'gami_points', $points_earn, true );
		add_post_meta( $post_id, 'gami_acquired_post_type', 'buying product', true );
		add_post_meta( $post_id, 'gami_acquired_post_id', $order_id, true );

		//error_log( "Order complete for order $order_id", 0 );
		
	}


	public static function gimi_item_profile() {
		add_rewrite_endpoint( 'gimification-profile', EP_ROOT | EP_PAGES );
	}

	

	public static function gimi_account_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		// Insert your custom endpoint.
		$items['gimification-profile'] = __( 'Gimification', 'woocommerce' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}
	
	public static function gimi_myaccount_itemcontent() {
		$gami_user = self::gimi_user_profile();
		
		 echo "<pre>";
		print_r($gami_user);
		echo "</pre>";
		
			?>
			<input type="text" value="<?=$gami_user['first_name']?>" name="user_email" />
			<input type="text" value="<?=$gami_user['data']->user_email?>" name="first_name" />
			<?php
	
	}




	public static function gimi_user_profile(){

		global $woocommerce;
		global $gimi_user_data; 
		echo "fgsdf";
		print_r($gimi_user_data);
		
		if (wp_get_current_user()){
		$gimi_user = wp_get_current_user();
		$user_info = (array)get_userdata($gimi_user->data->ID);
		$user_meta = get_user_meta($gimi_user->data->ID, false);
		$user_meta = array_filter( array_map( function( $a ) {
				return $a[0];
			}, $user_meta ) );
		

		$aprofile = array_merge($user_info,$user_meta);
		return $aprofile;
	
		}
	} 	
	
	/**
	   * Check woocommerce dependency
	 */
	 
	public static function check_plugin_activated() {
		
		//check woocommerce plugin is activated or else return false
		add_rewrite_endpoint( 'gimification-profile', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	
		$plugin = is_plugin_active("woocommerce/woocommerce.php");
		if (!$plugin) {
		  deactivate_plugins(plugin_basename(__FILE__));
		  add_action('admin_notices', array(__CLASS__, 'sample_notices'));
		  if (isset($_GET['activate']))
			unset($_GET['activate']);
		}
		else {
		  self::activate_plugin();
		  add_action('admin_notices','sample_notices');
		}
		
		// check buddypress dependency
		$bp_active = bp_is_active('xprofile');
		 
	  }
	  
	  public static function sample_notices() {
		  ?>
		  <div class="notice notice-success is-dismissible">
			<p><?php _e('Congratulations, you did the admin notice!', 'shapeSpace'); ?></p>
		</div>
		  <?php
	  }
	  
	  /**
	   * Things to do when the plugin is deactivated
	   */
	 public static function uninstall() {
		$products = get_posts(array(
		  'post_type'      => array('product', 'product_variation'),
		  'posts_per_page' => -1,
		  'fields'         => 'ids'
		));
		
		//Delete post meta related with the plugin
		/* foreach ($products as $id) {
		  //delete add setting 
		} */
	 }
  
}
?>