<?php
include_once 'gimification.php';


class gami_badge extends GIMIFICATION  {
	
	public function __construct(){
		add_action('init', array(&$this, 'badge_post_type'));
		
		//meta boxes
		add_action( 'add_meta_boxes', array( &$this, 'badge_meta_box' ) );
		add_action( 'save_post', array( &$this, 'badge_achievement_data_save' ) );
	}

	public function badge_post_type(){
		//Badges
		register_post_type( 'badge',
			array(
				'labels' => array(
					'name'               => _x( 'Badge', 'post type general name', 'gamification-plugin' ),		
					'singular_name'      => _x( 'Badge', 'post type singular name', 'gamification-plugin' ),
					'menu_name'          => _x( 'Badge', 'admin menu', 'gamification-plugin' ),
					'name_admin_bar'     => _x( 'Badge', 'add new on admin bar', 'gamification-plugin' ),
					'add_new'            => _x( 'Add New', 'Badge', 'gamification-plugin' ),
					'add_new_item'       => __( 'Add New Badge', 'gamification-plugin' ),
					'new_item'           => __( 'New Badge', 'gamification-plugin' ),
					'edit_item'          => __( 'Edit Badge', 'gamification-plugin' ),
					'view_item'          => __( 'View Badge', 'gamification-plugin' ),
					'all_items'          => __( 'Gami Badges', 'gamification-plugin' ),
					'search_items'       => __( 'Search Badge', 'gamification-plugin' ),
					'parent_item_colon'  => __( 'Parent Badge:', 'gamification-plugin' ),
					'not_found'          => __( 'No Badge found.', 'gamification-plugin' ),
					'not_found_in_trash' => __( 'No Badge found in Trash.', 'gamification-plugin' )
				),
				'public' => true,
				'show_in_menu' => 'gami',
				'has_archive' => true,
				'hierarchical' => false,
				'query_var' => true,
				'rewrite' => array('slug' => 'badge'),
				'supports' => array(
					'title',
					'editor',
					'thumbnail',
					'page-attributes'
				)
			)
		);
	}
	/**
     * Adds the meta box container.
     */
    public function badge_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        //$post_types = array( 'badge', 'page' );
        $post_types = 'badge';
 
        if ( $post_type == $post_types )  {
            add_meta_box(
                'achievement_data',
                __( 'Achievement Data', 'gamification' ),
                array( $this, 'badge_meta_box_achievement_data_content' ),
                $post_type,
                'advanced',
                'high'
            );
        }
    }
	
	/* badge save */
	function badge_achievement_data_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['gami_meta_achievement_box_nonce'] ) || ! wp_verify_nonce( $_POST['gami_meta_achievement_box_nonce'], 'gami_meta_achievement_box' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		if ( isset( $_POST['gami_level_points'] ) )
			update_post_meta( $post_id, 'gami_level_points', esc_attr( $_POST['gami_level_points'] ) );
		
		if ( isset( $_POST['gami_earn_by'] ) )
			update_post_meta( $post_id, 'gami_earn_by', esc_attr( $_POST['gami_earn_by'] ) );
	
		if ( isset( $_POST['gami_actions'] ) ){
			$numItems = count($_POST['gami_actions']);
			$i = 0;
			foreach($_POST['gami_actions'] as $item)
			{
				
				$gami_actions = $gami_actions.$sep.$item;
				$sep = (++$i === $numItems)?'':",";
			}
			echo $gami_actions;
			update_post_meta( $post_id, 'gami_actions', esc_attr( $gami_actions) );
		}
	
		if ( isset( $_POST['gami_congrationlation_text'] ) )
			update_post_meta( $post_id, 'gami_congrationlation_text', esc_attr( $_POST['gami_congrationlation_text'] ) );
	
		if ( isset( $_POST['show_to_user'] ) )
			update_post_meta( $post_id, 'show_to_user', esc_attr( $_POST['show_to_user'] ) );
	
	}
	
	public function badge_meta_box_achievement_data_content($post){
		global $badge_earn_by, $show_to_user;
		
		// Add an nonce field so we can check for it later.
        wp_nonce_field( 'gami_meta_achievement_box', 'gami_meta_achievement_box_nonce' );
		
		// Use get_post_meta to retrieve an existing value from the database.
        $gami_level_points = get_post_meta( $post->ID, 'gami_level_points', true );
        $gami_earn_by = get_post_meta( $post->ID, 'gami_earn_by',true);
        $gami_actions = get_post_meta( $post->ID, 'gami_actions',true);
		
		//print_r($gami_actions);
		$gami_action_arr = explode(',',$gami_actions);
		
        $gami_congrationlation_text = get_post_meta( $post->ID, 'gami_congrationlation_text', true );
        $gami_show_to_user = get_post_meta( $post->ID, 'show_to_user', true );
 
        // Display the form, using the current value.
        ?>
		<script>
		jQuery(document).ready(function(){
			jQuery('#gami_earn_by').change(function(){
				
				if (jQuery(this).val()=='completing_actions'){
					jQuery('#gami_actions_list').removeClass('gami_action_hide');
					jQuery('#gami_actions_list').addClass('gami_action_show');
				}else{
					jQuery('#gami_actions_list').removeClass('gami_action_show');
					jQuery('#gami_actions_list').addClass('gami_action_hide');
				}
			});
		});
		</script>
		<div class="achievement-metabox">
		<p>
		To set an image use the Achievement Image metabox to the right. For best results, use a square .png file with a transparent background, at least 200x200 pixels.
.
		</p>
		<hr>
		<p>
        <label for="gami_level_points">
            <?php _e( 'Level Points', 'gamification' ); ?>
        </label>
        <input type="text" id="gami_level_points" name="gami_level_points" value="<?php echo esc_attr( $gami_level_points ); ?>" size="25" />
		</p>
		<hr>
		<p>
        <label for="gami_earn_by">
            <?php _e( 'Earn By', 'gamification' ); ?>
        </label>
		<select id="gami_earn_by"  name="gami_earn_by" >		
		<?php
		foreach ($badge_earn_by as $ky=>$vl){
			$selected_item = ($gami_earn_by==$ky)? "selected" :"";
			echo "<option value=\"{$ky}\" $selected_item>{$vl}</option>";
		}
		
		?>
		</select>
        
		</p>
		<?php $completing_actions_show =  ($selected_item=='completing_actions')?"gami_action_show":"gami_action_hide"; ?>
		
		<div id="gami_actions_list" class="<?=$completing_actions_show?>">
		<hr>
		<div>
            <strong>Available Actions</strong>
        </div>
		<div>
		<ul>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_signin" <?php echo (in_array('gami_action_signin',$gami_action_arr))?" checked":"";?> > Sign In</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_register" <?php echo (in_array('gami_action_register',$gami_action_arr))?" checked":"";?> > Register Account</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_joingroup" <?php echo (in_array('gami_action_joingroup',$gami_action_arr))?" checked":"";?> > Join Vendor</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_postreview" <?php echo (in_array('gami_action_postreview',$gami_action_arr))?" checked":"";?> > Post review</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_postreview" <?php echo (in_array('gami_action_postreview',$gami_action_arr))?" checked":"";?> > Post comments (approval only)</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_sellproduct" <?php echo (in_array('gami_action_sellproduct',$gami_action_arr))?" checked":"";?> > Sell a products</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_postactivity" <?php echo (in_array('gami_action_postactivity',$gami_action_arr))?" checked":"";?> > Post Activity</li>
			<li><input type="checkbox" name="gami_actions[]" value="gami_action_buyproduct" <?php echo (in_array('gami_action_buyproduct',$gami_action_arr))?" checked":"";?> > Buy a products</li>
		</ul>
		</div>
		</div>

		<hr>
		<p>
		<div style="margin-bottom:15px">
            <strong>Congratulations Text</strong>
        </div>
        
		<textarea id="gami_congrationlation_text" name="gami_congrationlation_text" style="width:100%" rows="10" ><?php echo esc_attr( $gami_congrationlation_text ); ?></textarea>
       
		</p>
		<p>
		<label for="gami_earn_by">
            <?php _e( 'Hidden?', 'gamification' ); ?>
        </label>
		<select id="show_to_user"  name="show_to_user" >		
		<?php
		foreach ($show_to_user as $ky=>$vl){
			$selected_item = ($gami_show_to_user==$ky)? "selected" :"";
			echo "<option value=\"{$ky}\" $selected_item>{$vl}</option>";
		}
		
		?>
		</select>		
		</p>
		
		</div>
	   <?php
	}
	
}
?>