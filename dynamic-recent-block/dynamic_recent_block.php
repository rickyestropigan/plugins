<?php
/**
 * Plugin Name: Dynamic Recent Block
 * Plugin URI: 
 * Description: This plugin will display the recent articles or post visited by the current user.
 * Version:1.3.0
 * Author: REstropigan
 * Author URI: 
 * License: 
 */
 function drb_plugin_scripts_styles() {

	wp_register_style('admin_plugin_style_dynamic', plugins_url('/css/drb.css?t='.time(),__FILE__ ));
	wp_register_style('admin_plugin_bootstrap_style_dynamic', plugins_url('/css/bootstrap_grid.css?t='.time(),__FILE__ ));
	
	wp_enqueue_style('admin_plugin_style_dynamic');
	wp_enqueue_style('admin_plugin_bootstrap_style_dynamic');
}

add_action( 'admin_init','drb_plugin_scripts_styles');
add_action('wp_head', 'drb_plugin_scripts_styles');

function update_all_templates_to_new_drb()
{
    $args = array(
        'posts_per_page'   => -1,
        'post_type'        => array("post","page"),
        'suppress_filters' => true 
    );
    $posts_array = get_posts( $args );
    foreach($posts_array as $post_array)
    {
		$views = get_post_meta($post_array->ID,"mp_views",true);
		if(empty($views) or !$views){
			update_post_meta($post_array->ID, 'mp_views', 0);
		} 
	}
	
	if(!session_id()) {
        session_start();
		
		$userID = md5(uniqid(rand(), true));
		$user_data = array(
			"id" => $userID,
			"post_visited" => array()
		);
		
		$userData = $_SESSION["user_data"];//6 hours
		if(empty($userData)){
			$_SESSION["user_data"] = $user_data;
		}
		
		
    }
	//session_destroy();
	
}
add_action('init', 'update_all_templates_to_new_drb'); 

function count_views_drb() {
    if(is_single()){
		$id = get_the_ID();
		$views = get_post_meta($id,"mp_views",true);
		$new_views = $views+1;
		update_post_meta($id,"mp_views",$new_views,$views);
		update_post_meta($id,"mp_time_viewed",time());
		
		$old_userData = $_SESSION["user_data"];
		$old_userData['post_visited'] = count($old_userData['post_visited']) == 0 ? array():$old_userData['post_visited'];
		
		if(in_array($id,$old_userData["post_visited"])){
			foreach($old_userData["post_visited"] as $k => $v){
				if($v == $id){
					unset($old_userData["post_visited"][$k]); //remove similar viewed articles
				}
			}
		}
		
		array_push($old_userData['post_visited'],$id);
		krsort($old_userData['post_visited']);
	//	$old_userData["post_visited"] = array_push($old_userData["post_visited"],$post_id);
	
		$_SESSION["user_data"] = $old_userData;
	}
}
add_action( 'wp_head', 'count_views_drb' );

function dynamic_block_shortcode_drb( $atts ) {
	$post_ids = $_SESSION["user_data"]["post_visited"];
	$count = count($post_ids);
	$post_ids = $count > 5 ? array_slice($post_ids, 0, 5, true):$post_ids;
	
	if($count > 0)
	{
		$args = array(
			'post__in' => $post_ids,
			'order' => 'DESC',
			"meta_key" => "mp_time_viewed",
			"orderby" => "meta_value_num"
		);

		$posts = get_posts($args);
		
		switch($count){
			case 1:
				$class = "one-whole";
			break;
			
			case 2:
				$class = "col-sm-6";
			break;
			
			case 3:
				$class = "col-sm-6 one-third";
			break;
			
			case 4:
				$class = "col-sm-6 one-fourth";
			break;
			
			default:
				$class = "col-sm-6 one-fifth";
		}
	}else
	{
		//display most viewed
		$data = array(
			'post_status' => 'publish',
			'post_type' => 'post',
			'posts_per_page' => 5,
			'order' => 'DESC',
			"meta_key" => "mp_views",
			"orderby" => "meta_value_num"
		);
		
		$query = new WP_Query($data);
		$posts = $query->posts;
		$class = "col-sm-6 one-fifth";
	}
	
	$data = "";
	foreach($posts as $post){
		$link = get_permalink($post->ID);
		$image = "<img src = '".get_the_post_thumbnail_url( $post->ID )."' alt = '".$post->post_title."' class = 'drb-featured-image cover-fit-img'/>";
		
		$data .= "<div class = 'drb-widget dynamic-block'>
			<div class = '".$class." drb-column'>
				<div class = 'drb-container'>
					<a href = '".$link."' class = 'drb-image-link'>
						".$image."
						<h3 class = 'drb-title drb-box'><span>".$post->post_title."</span></h3>
					</a>
				</div>
			</div>
		</div>";
	}

	$data .= "<div class = 'clearfix block-margin'></div>";
	return $data;
}
add_shortcode( 'dynamic_recent_block', 'dynamic_block_shortcode_drb' );