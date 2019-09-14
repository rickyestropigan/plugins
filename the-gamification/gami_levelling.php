<?php
include_once 'gimification.php';


class gami_levelling extends GIMIFICATION  {
	
	public function __construct(){
		add_action('init', array(&$this, 'levelling_post_type'));
		
		//meta boxes
		add_action( 'add_meta_boxes', array( &$this, 'levelling_meta_box' ) );
		add_action( 'save_post', array( &$this, 'levelling_attribiutes_data_save' ) );
		
		//listing new column
		add_filter('manage_level_posts_columns', array(&$this,'levelling_list_column'));
		add_action('manage_posts_custom_column', array(&$this,'levelling_list_column_content'), 10, 2);

	}
	
	// ADD NEW COLUMN
	public function levelling_list_column($columns) {
		
		unset(
			$columns['date'],
			$columns['title']
		);
		$new_columns = array(
			'title' => __('Level Name', 'Gamification'),
			'experience_points' => __('Experience Points', 'Gamification'),
			'requirement_level' => __('Requirement', 'Gamification'),
			'date' => __('Date Acquired', 'Gamification'),
		);
		
		return array_merge($columns, $new_columns);

	}

	public function levelling_list_column_content( $column, $post_id ) {
		global $levelling_requirement;
		
		switch ( $column ) {
			case 'experience_points':
				
				//print_r(get_post_meta($post_id));

				$gami_experience_points = get_post_meta( $post_id, 'gami_experience_points', true );
				echo $gami_experience_points;
				
				break;

			case 'requirement_level':
				$gami_level_requirement = get_post_meta( $post_id, 'gami_level_requirement', true );
				echo $levelling_requirement[$gami_level_requirement];

				break;
		}
	}
	
	public function levelling_post_type(){
	register_post_type( 'level',
		array(
			'labels' => array(
				'name'               => _x( 'Levelling', 'post type general name', 'gamification-plugin' ),		
				'singular_name'      => _x( 'Level', 'post type singular name', 'gamification-plugin' ),
				'menu_name'          => _x( 'Level', 'admin menu', 'gamification-plugin' ),
				'name_admin_bar'     => _x( 'Level', 'add new on admin bar', 'gamification-plugin' ),
				'add_new'            => _x( 'Add New', 'Level', 'gamification-plugin' ),
				'add_new_item'       => __( 'Add New Level', 'gamification-plugin' ),
				'new_item'           => __( 'New Level', 'gamification-plugin' ),
				'edit_item'          => __( 'Edit Level', 'gamification-plugin' ),
				'view_item'          => __( 'View Level', 'gamification-plugin' ),
				'all_items'          => __( 'Gami Levelling', 'gamification-plugin' ),
				'search_items'       => __( 'Search Level', 'gamification-plugin' ),
				'parent_item_colon'  => __( 'Parent Level:', 'gamification-plugin' ),
				'not_found'          => __( 'No Level found.', 'gamification-plugin' ),
				'not_found_in_trash' => __( 'No Level found in Trash.', 'gamification-plugin' )
			),
			'public' => true,
			'show_in_menu' => 'gami',
			'has_archive' => true,
			'hierarchical' => false,
			'query_var' => true,
			'rewrite' => array('slug' => 'level'),
			'supports' => array(
				'title',
				'thumbnail',
				'excerpt',
				'page-attributes'
			)
		)
	);
	}
	
	/**
     * Adds the meta box container.
     */
    public function levelling_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        //$post_types = array( 'badge', 'page' );
        $post_types = 'level';
 
        if ( $post_type == $post_types )  {
            add_meta_box(
                'achievement_data',
                __( 'Levelling Data', 'gamification' ),
                array( $this, 'levelling_meta_box_attributes_data_content' ),
                $post_type,
                'advanced',
                'high'
            );
        }
    }

	public function levelling_meta_box_attributes_data_content($post){
		global $badge_earn_by, $show_to_user, $levelling_requirement, $levelling_bonuses;
		
		// Add an nonce field so we can check for it later.
        wp_nonce_field( 'gami_meta_levelling_box', 'gami_meta_levelling_box_nonce' );
		
		// Use get_post_meta to retrieve an existing value from the database.
        $gami_experience_points = get_post_meta( $post->ID, 'gami_experience_points', true );
        $gami_level_requirement = get_post_meta( $post->ID, 'gami_level_requirement',true);
        $gami_level_bonus = get_post_meta( $post->ID, 'gami_level_bonus',true);

		
        $gami_congrationlation_text = get_post_meta( $post->ID, 'gami_congrationlation_text', true );
        $gami_show_to_user = get_post_meta( $post->ID, 'show_to_user', true );
 
        // Display the form, using the current value.
        ?>
	
		<div class="achievement-metabox">
		<p>
		Set you levelling data below.
		</p>
		<hr>
		<p>
        <label for="gami_experience_points">
            <?php _e( 'Experience Points', 'gamification' ); ?>
        </label>
        <input type="text" id="gami_experience_points" name="gami_experience_points" value="<?php echo esc_attr( $gami_experience_points ); ?>" size="25" />
		</p>
		<hr>
		
		<p>
        <label for="gami_level_requirement">
            <?php _e( 'Requirement', 'gamification' ); ?>
        </label>
		<select id="gami_level_requirement"  name="gami_level_requirement" >
		<option value="" disabled selected hidden>Please Choose...</option>		
		<?php
		foreach ($levelling_requirement as $ky=>$vl){
			$selected_item = ($gami_level_requirement==$ky)? "selected" :"";
			echo "<option value=\"{$ky}\" $selected_item>{$vl}</option>";
		}
		
		?>
		</select>
        
		</p>
		<hr>
		
		<p>
        <label for="gami_level_bonus">
            <?php _e( 'Bonus', 'gamification' ); ?>
        </label>
		<select id="gami_level_bonus"  name="gami_level_bonus" >
		<option value="" disabled selected hidden>Please Choose...</option>		
		<?php
		foreach ($levelling_bonuses as $ky=>$vl){
			$selected_item = ($gami_level_bonus==$ky)? "selected" :"";
			echo "<option value=\"{$ky}\" $selected_item>{$vl}</option>";
		}
		
		?>
		</select>
        
		</p>
		<hr>
		
		<p>
		<div style="margin-bottom:15px">
            <strong>Congratulations Text</strong>
        </div>
        
		<textarea id="gami_congrationlation_text" name="gami_congrationlation_text" style="width:100%" rows="10" ><?php echo esc_attr( $gami_congrationlation_text ); ?></textarea>
       
		</p>
		<p>
		<label for="show_to_user">
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

	/* badge save */
	function levelling_attribiutes_data_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['gami_meta_levelling_box_nonce'] ) || ! wp_verify_nonce( $_POST['gami_meta_levelling_box_nonce'], 'gami_meta_levelling_box' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		if ( isset( $_POST['gami_experience_points'] ) )
			update_post_meta( $post_id, 'gami_experience_points', esc_attr( $_POST['gami_experience_points'] ) );
		
		if ( isset( $_POST['gami_level_requirement'] ) )
			update_post_meta( $post_id, 'gami_level_requirement', esc_attr( $_POST['gami_level_requirement'] ) );
	
	
		if ( isset( $_POST['gami_level_bonus'] ) )
			update_post_meta( $post_id, 'gami_level_bonus', esc_attr( $_POST['gami_level_bonus'] ) );
	
	
		if ( isset( $_POST['gami_congrationlation_text'] ) )
			update_post_meta( $post_id, 'gami_congrationlation_text', esc_attr( $_POST['gami_congrationlation_text'] ) );
	
		if ( isset( $_POST['show_to_user'] ) )
			update_post_meta( $post_id, 'show_to_user', esc_attr( $_POST['show_to_user'] ) );
	
	}
	
}
?>