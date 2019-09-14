<?php

include_once 'gimification.php';

class gami_settings extends GIMIFICATION  {
  /**
   * Function to register plugin settings
   * attached to: admin_init action
   */
   
  public function __construct(){
	  
	//Yard_Settings
    add_action('admin_init',array($this, 'register_my_setting'));
    add_action('admin_menu',array($this, 'settings_page')); 
	
	// add meta box
	add_action('add_meta_boxes',array($this, 'gimi_add_meta_box_posts'));
	//add_action('add_meta_boxes',array($this, 'gimi_add_meta_box_products'));
	add_action('add_meta_boxes',array($this, 'gimi_add_meta_box_groups'));
	add_action('init',array(__CLASS__, 'gimi_cutomPost'));
	add_action( 'admin_menu', array( $this, 'gami_plugin_menu' ) );
  }
  
  public function gami_plugin_menu() {

		// Set minimum role setting for menus
		$minimum_role = badgeos_get_manager_capability();

		// Create main menu
		add_menu_page( 'Gamification', 'Gamification', 'manage_options', 'gami', 'gamification', '', 110 );

		// Create submenu items
		add_submenu_page( 'gami', __( 'Gamification Ranking', 'gamification' ), __( 'Users Rank', 'gamification' ), $minimum_role, 'gamification_ranking', array($this,'gamification_ranking_page') );
		add_submenu_page( 'gami', __( 'Gamification Settings', 'gamification' ), __( 'Settings', 'gamification' ), $minimum_role, 'gamification_settings', array($this,'gamification_settings_page') );
		
		add_submenu_page( 'gami', __( 'Help / Support', 'gamification' ), __( 'Help / Support', 'gamification' ), $minimum_role, 'gamification_sub_help_support', array($this,'gamification_help_support_page') );

	}
	
  public function gamification_help_support_page(){
	
  }
  public function gamification_ranking_page(){
	global $wp_list_table, $wpdb;
	
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
	
	//Prepare Table of elements
	$wp_list_table = new Link_List_Table();
	$wp_list_table->prepare_items();
	//Table of elements
	$wp_list_table->display();
	
  }
  public function gamification_settings_page(){
	  
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
	
    ?>
	<!--
	<script>
		jQuery("document").ready(function() {
			jQuery( "#tabs" ).tabs();
		});
	</script>
	-->
		<div class="wrap">
		  <?php screen_icon(); ?>
			<!-- Create a header in the default WordPress 'wrap' container -->
			
			<h2>Gamification Options</h2>
			
			<?php
			$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'gimi-points-settings';
			if(isset($_GET['tab']))
				$active_tab = $_GET['tab'];
			?>
			
			<div class="nav-tab-wrapper">
				<ul>
					<li><a href="?page=Gimi-listing-settings&amp;tab=gimi-points-settings" class="nav-tab <?php echo $active_tab == 'gimi-settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Gamification Settings', 'gimi_settings'); ?></a></li>
					<li><a href="?page=Gimi-listing-settings&amp;tab=gimi-users-exp" class="nav-tab <?php echo $active_tab == 'gimi-users-exp' ? 'nav-tab-active' : ''; ?>"><?php _e('User Experience', 'gimi_settings'); ?></a></li>
					<li><a href="?page=Gimi-listing-settings&amp;tab=gimi-user-activities" class="nav-tab <?php echo $active_tab == 'gimi-user-activities' ? 'nav-tab-active' : ''; ?>"><?php _e('User Activities', 'gimi_settings'); ?></a></li>
				</ul>
				
				<br>
				<?php if($active_tab == 'gimi-points-settings') { ?>
				<div id="poststuff" class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h3><?php _e('Main Settings', 'gimi_settings'); ?></h3>
						<div class="inside">
							<p>Options for points system</p>
							<?php self::gimi_setting_page(); ?>
						</div>
					</div>

				</div>
				<?php } if($active_tab == 'gimi-users-exp') { ?>
					<div id="poststuff" class="ui-sortable meta-box-sortables">
						<div class="postbox">
							<h3><?php _e('User Ranking', 'gimi_settings'); ?></h3>
							<div class="inside">
								<p>Top Site Performer</p>
								<?php self::gimi_user_experience(); ?>
							</div>
						</div>
					</div>
				<?php } if($active_tab == 'gimi-user-activities') { ?>
					<div id="poststuff" class="ui-sortable meta-box-sortables">
						<div class="postbox">
							<h3><?php _e('Points', 'gimi_settings'); ?></h3>
							<div class="inside">
								<p>Points accumulated by users based on their buddypress activities.</p>
								<?php self::gimi_user_activities() ?>
							</div>
						</div>
					</div>
				<?php } ?>
				
				
			</div>

		</div>
	   <?php 
	  
  }
  
  public static function gimi_cutomPost() {
	
	//All points
	register_post_type( 'gamification',
		array(
			'labels' => array(
				'name'               => _x( 'Gamification', 'post type general name', 'gamification-plugin' ),		
				'singular_name'      => _x( 'Gamification', 'post type singular name', 'gamification-plugin' ),
				'menu_name'          => _x( 'Gamification', 'admin menu', 'gamification-plugin' ),
				'name_admin_bar'     => _x( 'Gamification', 'add new on admin bar', 'gamification-plugin' ),
				'add_new'            => _x( 'Add New', 'Gamification', 'gamification-plugin' ),
				'add_new_item'       => __( 'Add New Gamification', 'gamification-plugin' ),
				'new_item'           => __( 'New Gamification', 'gamification-plugin' ),
				'edit_item'          => __( 'Edit Gamification', 'gamification-plugin' ),
				'view_item'          => __( 'View Gamification', 'gamification-plugin' ),
				'all_items'          => __( 'Gami Points', 'gamification-plugin' ),
				'search_items'       => __( 'Search Gamification', 'gamification-plugin' ),
				'parent_item_colon'  => __( 'Parent Gamification:', 'gamification-plugin' ),
				'not_found'          => __( 'No Gamification found.', 'gamification-plugin' ),
				'not_found_in_trash' => __( 'No Gamification found in Trash.', 'gamification-plugin' )
			),
			'public' => true,
			'show_in_menu' => 'gami',
			'has_archive' => true,
			'hierarchical' => false,
			'query_var' => true,
			'rewrite' => array('slug' => 'gamification'),
			'supports' => array(
				'title',
				'editor',
				'excerpt',
				'trackbacks',
				'custom-fields',
				'comments',
				'revisions',
				'thumbnail',
				'author',
				'page-attributes'
			)
		)
	);
	
	
}
  
  ########################### meta box posts ###########################
  public function gimi_add_meta_box_groups() {

		add_meta_box(
			'gimi_posting',
			'Gamification points',
			array(__CLASS__, 'gimi_display_meta_box_groups'),
			'product,post',
			'side',
			'high'
		);	
		
		add_meta_box(
			'gimi_points',
			'Gamification points',
			array(__CLASS__, 'gimi_display_points'),
			'gamification',
			'side',
			'high'
		);	
  }
  
  public static function gami_meta_box_product_points($post, $metabox) {
	   global $gimi_options;
	  
	     // Output last time the post was modified.
    echo 'Last Modified: ' . $post->post_modified;
 
    // Output 'this'.
    echo $metabox['args']['foo'];
 
    // Output 'that'.
    echo $metabox['args']['bar'];
 
    // Output value of custom field.
    echo get_post_meta( $post->ID, 'wpdocs_custom_field', true );
	
  }
  
  public static function gimi_display_meta_box_groups() {
	   global $gimi_options;
	  
	  wp_nonce_field( plugin_basename( __FILE__ ), 'cpmb-nonce-field' );

	?>
		<p><?php echo $gimi_options['posts_points'];  ?> points</p>
	<?php
  }
  
  public static function gimi_display_points() {
	   global $gimi_options;
	  
	  wp_nonce_field( plugin_basename( __FILE__ ), 'cpmb-nonce-field' );

	?>
		<p>Acquired point: <?php echo $gimi_options['posts_points'];  ?></p>
	<?php
  }
  
  ########################### meta box posts ###########################
  public function gimi_add_meta_box_posts() {
	  add_meta_box(
			'gimi_posting',
			'Gamification',
			array(__CLASS__, 'gimi_display_meta_box_posts'),
			'post',
			'side',
			'high'
		);
  }
  
  public static function gimi_display_meta_box_posts() {
	   global $gimi_options;
	  
	  wp_nonce_field( plugin_basename( __FILE__ ), 'cpmb-nonce-field' );

	?>
		<p>Points for this post: <?php echo $gimi_options['posts_points'];  ?></p>
	<?php
  }
  
    ########################### meta box products ###########################
	
	 public function gimi_add_meta_box_products() {
	  add_meta_box(
			'gimi_posting',
			'Gamification',
			array(__CLASS__, 'gimi_display_meta_box_products'),
			'product',
			'side',
			'high'
		);
  }
  
  public static function gimi_display_meta_box_products() {
	   global $gimi_options;
	  
	  wp_nonce_field( plugin_basename( __FILE__ ), 'cpmb-nonce-field' );

	?>
		<p>Points for this product: <?php echo $gimi_options['product_points'];  ?></p>
	<?php
  }
  
  /* sample notices
  public function sample_admin_notice__success() {
	   ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Done! notices', 'arnel notices' ); ?></p>
    </div>
    <?php
  } */
  
  public function register_my_setting() { 
  		 wp_enqueue_script('thickbox');
		 wp_enqueue_style('thickbox');
		 wp_enqueue_script("jquery-ui-core");
		 wp_enqueue_script("jquery-ui-dialog");
		 wp_enqueue_script("jquery-ui-sortable");
		 wp_enqueue_script("jquery-ui-tabs");
		
		//css
		 
		wp_register_style('gimi-admin-styles', plugins_url('/css/style-admin.css', __FILE__), array(), '1');
		wp_enqueue_style('gimi-admin-styles');

		wp_register_script('gimi-admin-js', plugins_url('/js/scripts.js', __FILE__), array(), '1');
		wp_enqueue_script('gimi-admin-js');
		
		wp_enqueue_style('gimi-admin-jqui',
                'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css',
                false,
                '1.0',
                false);
				
		// register settings groups
		register_setting('gimi_settings_group','gimi_settings');
   }


  public static function settings_page() {
   
  }
  

  /**
   * Function to add settngs link in plugins page
   * attached to: plugin_action_links_<plugin> filter
   */
  public static function add_settings_link( $links ) {
    ob_start();
    ?>
    <a href="options-general.php?page=ads-listing-settings">Settings</a>
    <?php
    $settings_link = ob_get_contents();
    ob_end_clean();
    array_push( $links, $settings_link );
    ob_start();
    ?>
   
    <?php
    $docs_link = ob_get_contents();
    ob_end_clean();
    array_push( $links, $docs_link);
    return $links;
  }

  private static function gimi_setting_page(){
	  
	  global $gimi_options;
	  
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php settings_fields( 'gimi_settings_group' ); ?>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Products</th>
							<td>
								<input id="gimi_settings[product_enable]" name="gimi_settings[product_enable]" type="checkbox" value="1" <?php echo isset($gimi_options['product_enable']) ? "checked" : false; ?> />  								
								<label class="description" for="gimi_settings[product_enable]"><?php _e('Add points via selling products','gimi_domain'); ?></label> 
							</td>
						</tr>
						<tr>
							<th scope="row">Points</th>
							<td>
								<input id="gimi_settings[product_points]" name="gimi_settings[product_points]" type="number" step="1" class="small-text" value="<?php echo isset($gimi_options['product_points']) ? $gimi_options['product_points'] : ""; ?>" >
								<p class="description">Desired points for selling products</p>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Members</th>
							<td>
								<input id="gimi_settings[group_enable]" name="gimi_settings[group_enable]" type="checkbox" value="1" <?php echo isset($gimi_options['group_enable']) ? "checked" : false; ?> />
								<label class="description" for="gimi_settings[group_enable]">Add points via joining groups</label>
							</td>
						</tr>
						<tr>
							<th scope="row">Points</th>
							<td>
								<input id="gimi_settings[group_points]" name="gimi_settings[group_points]" type="number" step="1" class="small-text" value="<?php echo isset($gimi_options['group_points']) ? $gimi_options['group_points'] : ""; ?>" />
								<p class="description">Desired point for joining groups</p>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="form-table"> 
					<tbody>
						<tr>
							<th scope="row">User Post</th>
							<td>
								<input id="gimi_settings[posts_enable]" name="gimi_settings[posts_enable]" type="checkbox" value="1"  <?php echo isset($gimi_options['posts_enable']) ? "checked" : false; ?> />
								<label class="description" for="gimi_settings[posts_enable]">Add points via number of user post</label>
							</td>
						</tr>
						<tr>
							<th scope="row">Points</th> 
							<td>
								<input id="gimi_settings[posts_post]" name="gimi_settings[posts_post]" type="number" step="1" class="small-text" value="<?php echo isset($gimi_options['posts_post']) ? $gimi_options['posts_post'] : ""; ?>" maxlength="2">
								<p class="description">Maximum number of posts to accumulate points</p>
								 
								<input id="gimi_settings[posts_points]" name="gimi_settings[posts_points]" type="number" step="1" class="small-text" value="<?php echo isset($gimi_options['posts_points']) ? $gimi_options['posts_points'] : ""; ?>" maxlength="2">
								<p class="description">Desired points for posting</p>
							</td> 
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
				</p>
			</form>
		</div>
		<?php
	  }
  
  public static function gimi_user_experience() {
	  //global $woocommerce;
	  
	  $gimi_users = get_users();
	  
	   ?>
			<table class="widefat">
			<thead>
				<tr>
					<th>Users</th>
					<th>Email</th>
					<th>Date of Membership</th>
					<th>Accumulated Points</th>
					<th>User Level</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Users</th>
					<th>Email</th>
					<th>Date of Membership</th>
					<th>Accumulated Points</th>
					<th>User Level</th>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($gimi_users as $user): ?>
			   <tr>
				 <td><?php echo $user->user_login;?></td>
				 <td><?php echo $user->user_email;?></td>
				 <td><?php echo $user->user_registered;?></td>
				 <td># points</td>
				 <td>Level #</td>
			   </tr>
			   <?php endforeach;?>
			</tbody>
			</table>
		  <?php
	  }
	  
	  public static function gimi_user_activities() {
		   global $bp;
		   
		   $gimi_users = get_users();
		   $gimi_activities = $bp;
		   
		   echo "<pre>";
		   print_r($bp->activity);
		   echo "</pre>";
		   
		   //if ($_POST['select_name'] === 'category_name') {
			echo (bp_has_activities( bp_ajax_querystring( 'ID' ).'&search_terms=34' ) );
			//}
		   
	  
	   ?>
	   <form method="POST">
			<p>Select User: 
				<select name="options">
					<?php foreach($gimi_users as $user): ?>
						<option value="<?php echo $user->ID; ?>"> <?php echo $user->user_login; ?> </option>
					<?php endforeach;?>
				</select>
				<input type="submit" name="btnSearch" id="btnSearch" class="button button-primary" value="View User Activities"  />
				<!--<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>-->
			</p> 
	   </form>
	   
	   <?php #if(isset($_POST['btnSearch'])): ?>
	   
	   <?php #echo $_POST['options']; ?>
	   
			<table class="widefat">
				<thead>
					<tr>
						<th>Activities</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Activities</th>
					</tr>
				</tfoot>
				<tbody>
					<?php #foreach($gimi_users as $user): ?>
				   <tr>
					 <td>Level #</td>
				   </tr>
				   <?php #endforeach;?>
				</tbody>
			</table>
	   <?php #endif;?>
			
		  <?php
	  }
	  
	//public static function get_user_activities($id=0) {
		
	//}
  
}
