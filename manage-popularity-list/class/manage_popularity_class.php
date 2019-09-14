<?php
 // Creating the widget 
class manage_popularity_widget extends WP_Widget {
	
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'manage_popularity_widget', 

			// Widget name will appear in UI
			__('Manage Popularity Widget', 'manage_popularity_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Widget to manage popularity of post/page', 'manage_popularity_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$rows = $instance['rows'];
		$filters = $instance['filters'];
		$class = empty($instance['text']) ? '' : $instance['text'];
		
		$instance[ 'withImage' ] = $instance[ 'withImage' ]?"on":"";
		$instance[ 'withText' ] = $instance[ 'withText' ]?"on":"";
		$instance[ 'showDate' ] = $instance[ 'showDate' ]?"on":"";
		$instance[ 'withCategory' ] = $instance[ 'withCategory' ]?"on":"";
		$instance[ 'includePages' ] = $instance[ 'includePages' ]?"on":"";
	
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title']. $title.$args['after_title'];
	
		if($instance["includePages"] == 'on'){
			$data = array(
				'post_status' => 'publish',
				'post_type' => array("post","page"),
				'posts_per_page' => $rows,
				'order' => 'DESC'
			);
		}else
		{
			$data = array(
			'post_status' => 'publish',
			'post_type' => 'post',
			'posts_per_page' => $rows,
            'order' => 'DESC'
		);
		}
	
		
		
		if($filters == "recent_featured" OR $filters == "recent_tagged" OR $filters == "recent_cat"){
			$data["orderby"] = "ID";
		}else{
			$data["meta_key"] = "mp_views";
			$data["orderby"] = "meta_value_num";
		}
		
		if($filters == "popular_tagged" OR $filters == "recent_tagged"){
			$tag_id = $instance["tag_id"];
			$data["tag_id"] = $tag_id;
		}
		
		if($filters == "popular_cat" OR $filters == "recent_cat"){
			$categories = explode(",",$instance["selected_categories"]);
			$data["cat"] = $categories;
		}
		
		if($filters == "popular_featured" OR $filters == "recent_featured"){
			$data["meta_query"] = array(
					"relation" => "AND",
					array(
						"key" => "_is_ns_featured_post",
						"value" => "yes",
						"compare" => "="
					),
					array(
						"key" => "mp_is_viewed",
						"value" => "no",
						"compare" => "="
					)
				);
		}else{
			$data["meta_query"] = array(
				array(
					"key" => "mp_is_viewed",
					"value" => "no",
					"compare" => "="
				)
			);
		}
		
		/* echo "<pre>";
		print_r($data);
		print_r($instance);
		echo "</pre>"; */
		$query = new WP_Query($data);
		$posts = $query->posts;
		echo "<ul class = 'mpl-widget'>";
		
		foreach($posts as $post){
			display_results($instance,$post);
			update_post_meta($post->ID, 'mp_is_viewed', 'yes');
		}
		
		echo "</ul>";
		echo $content;
		
		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) || isset($instance['filters'])) {
			$title = $instance[ 'title' ];
			$rows = $instance[ 'rows' ];
			$filter = $instance['filters'];
		}
		else {
			$title = __( '', 'manage_popularity_widget_domain' );
			$rows = __( '5', 'manage_popularity_widget_domain' );
			$filter = __( 'unfiltered', 'manage_popularity_widget_domain' );
		}
		
		$tag_id = (isset($instance["tag_id"]))?$instance["tag_id"]:"";
		// Widget admin form
		$text = $instance['text'];   
		
		$instance[ 'withImage' ] = $instance[ 'withImage' ]?"on":"";
		$instance[ 'withText' ] = $instance[ 'withText' ]?"on":"";
		$instance[ 'showDate' ] = $instance[ 'showDate' ]?"on":"";
		$instance[ 'withCategory' ] = $instance[ 'withCategory' ]?"on":"";
		$instance[ 'includePages' ] = $instance[ 'includePages' ]?"on":"";
		
		$hasPlugin = false;
		if(is_plugin_active("ns-featured-posts/ns-featured-posts.php")){
			$hasPlugin = true;
		}
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'rows' ); ?>"><?php _e( 'No. of post/page to display:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'rows' ); ?>" name="<?php echo $this->get_field_name( 'rows' ); ?>" type="number" value="<?php echo esc_attr( $rows ); ?>" />
		</p>
		
		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?=$instance[ 'includePages' ]=="on"?"checked":""; ?> 
				id="<?php echo $this->get_field_id( 'includePages' ); ?>" 
				name="<?php echo $this->get_field_name( 'includePages' ); ?>" 
			/> 
			<label for="<?php echo $this->get_field_id( 'includePages' ); ?>">Include Pages:</label>
		</p>
		
		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?=$instance[ 'withCategory' ]=="on"?"checked":""; ?> 
				id="<?php echo $this->get_field_id( 'withCategory' ); ?>" 
				name="<?php echo $this->get_field_name( 'withCategory' ); ?>" 
			/> 
			<label for="<?php echo $this->get_field_id( 'withCategory' ); ?>">With Category</label>
		</p>
		
		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?=$instance[ 'withImage' ]=="on"?"checked":""; ?> 
				id="<?php echo $this->get_field_id( 'withImage' ); ?>" 
				name="<?php echo $this->get_field_name( 'withImage' ); ?>" 
			/> 
			<label for="<?php echo $this->get_field_id( 'withImage' ); ?>">With Image</label>
		</p>
		
		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?=$instance[ 'withText' ]=="on"?"checked":""; ?> 
				id="<?php echo $this->get_field_id( 'withText' ); ?>" 
				name="<?php echo $this->get_field_name( 'withText' ); ?>" 
			/> 
			<label for="<?php echo $this->get_field_id( 'withText' ); ?>">With Text</label>
		</p>
		
		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?=$instance[ 'showDate' ]=="on"?"checked":""; ?> 
				id="<?php echo $this->get_field_id( 'showDate' ); ?>" 
				name="<?php echo $this->get_field_name( 'showDate' ); ?>" 
			/> 
			<label for="<?php echo $this->get_field_id( 'showDate' ); ?>">Display Date</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'mplTheme' ); ?>">Select Theme</label>
			<select class="widefat" id = "<?php echo $this->get_field_id( 'mplTheme' ); ?>" name="<?php echo $this->get_field_name( 'mplTheme' ); ?>">
				<option value = "theme1"  <?=($instance['mplTheme']=="theme1")?"selected":"";?>>Theme 1</option>
				<option value = "theme2" <?=($instance['mplTheme']=="theme2")?"selected":"";?>>Theme 2</option>
				<option value = "theme3" <?=($instance['mplTheme']=="theme3")?"selected":"";?>>Theme 3</option>
				<option value = "theme4" <?=($instance['mplTheme']=="theme4")?"selected":"";?>>Theme 4</option>
				<option value = "theme4_1" <?=($instance['mplTheme']=="theme4_1")?"selected":"";?>>Theme 4.1</option>
				<option value = "theme5" <?=($instance['mplTheme']=="theme5")?"selected":"";?>>Theme 5</option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>">Lists Class: 
			<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" 
					 name="<?php echo $this->get_field_name('text'); ?>" type="text" 
					 value="<?php echo attribute_escape($text); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'filters' ); ?>"><?php _e( 'Filters:' ); ?></label>
			<br />
			<input 
				class = "radio-filter" 
				id="<?php echo $this->get_field_id( 'unfiltered' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "unfiltered" <?=($filter=="unfiltered")?"checked":""?> />
			<?php _e( 'Unfiltered' ); ?><br />
			
			<input 
				<?=$hasPlugin?"":"disabled";?>
				class = "radio-filter" 
				id="<?php echo $this->get_field_id( 'popular-featured' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "popular_featured" <?=($filter=="popular_featured")?"checked":""?> />
			<?php _e( 'Popular Featured Post' ); ?><br />
			
			<input 
				class = "radio-filter pop-tagged-post" 
				id="<?php echo $this->get_field_id( 'popular-tagged' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "popular_tagged" <?=($filter=="popular_tagged")?"checked":""?> />
			<?php _e( 'Popular Post Tagged' ); ?><br />
			
			<input 
				class = "radio-filter pop-cat-post" 
				id="<?php echo $this->get_field_id( 'popular-categories' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio"
				value = "popular_cat" <?=($filter=="popular_cat")?"checked":""?> />
			<?php _e( 'Popular Post from Categories' ); ?><br />
			
			<input 
				<?=$hasPlugin?"":"disabled";?>
				class = "radio-filter" 
				id="<?php echo $this->get_field_id( 'recent-featured' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "recent_featured" <?=($filter=="recent_featured")?"checked":""?> />
			<?php _e( 'Recent Featured Post' ); ?><br />
			
			<input 
				class = "radio-filter recent-tagged-post" 
				id="<?php echo $this->get_field_id( 'recent-tagged' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "recent_tagged" <?=($filter=="recent_tagged")?"checked":""?> />
			<?php _e( 'Recent Post Tagged' ); ?><br />
			
			<input 
				class = "radio-filter recent-cat-post" 
				id="<?php echo $this->get_field_id( 'recent-categories' ); ?>" 
				name="<?php echo $this->get_field_name( 'filters' ); ?>" 
				type="radio" 
				value = "recent_cat" <?=($filter=="recent_cat")?"checked":""?> />
			<?php _e( 'Recent Post from categories' ); ?><br />
			<?php //echo $tag_id."-  the selected tag";?>
			<?php //echo $filter."-  the filter";?>
		</p>
		
		<?php 
		if(!$hasPlugin){
			echo "<div class = 'update-nag'>
			<b>Popular Featured Post</b> and <b>Recent Featured Post</b> filters requires a plugin dependency to be enabled.
			It requires the <a href = 'https://wordpress.org/plugins/ns-featured-posts/'>NS Featured Plugin</a> that can be downloaded <a href = 'https://wordpress.org/plugins/ns-featured-posts/'>here.</a>
			</div><br /><br />";
		}
		?>
		
		<?php 
		$tags = get_tags();
		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC'
		) );
		?>
		<p class = "tagged-post hidden-widget-element" style = "display:<?=($filter == "popular_tagged"||$filter == "recent_tagged")?"block":"none";?>;">
			<label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Select Tag:' ); ?></label> 
			<select class="widefat" id = "<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag_id' ); ?>">
				<?php
				foreach($tags as $tag){
					$selected = ($tag->term_id == $tag_id)?"selected":"";
					echo "<option value = '".$tag->term_id."' ".$selected.">".$tag->name."</option>";
				}
				?>
			</select>
		</p>
		
		<p class = "categories-post hidden-widget-element" style = "display:<?=($filter == "popular_cat"||$filter == "recent_cat")?"block":"none";?>;">
			<label for="<?php echo $this->get_field_id( 'cat-post' ); ?>"><?php _e( 'Select Categories:' ); ?></label> <br />
			<?php 
			/*echo "<pre>";
			print_r($instance);
			echo "</pre>"; */
			$instance["categories"] = isset($instance["categories"])?$instance["categories"]:explode(",",$instance["selected_categories"]);
			foreach($categories as $category){
				$checked = "";
				if(is_array($instance["categories"])){
					if(in_array($category->term_id,$instance["categories"])){
						$checked = "checked";
					}
				}
				?>
				<input class = "mpl-categories" name="<?php echo $this->get_field_name( 'categories[]' ); ?>" type="checkbox" value = "<?=$category->term_id;?>" <?=$checked?>/><?php _e($category->name); ?><br />
				<?php
			}
			?>
			<input type = "hidden" class = "mpl-selected-categories" name = "<?php echo $this->get_field_name( 'selected_categories' ); ?>" />
		</p>
		<script>
		jQuery(function($){
			//alert("clicked");
			$(".radio-filter").click(function(){
				$(".hidden-widget-element").hide();
				console.log("clicked");
				if($(this).hasClass("pop-tagged-post") || $(this).hasClass("recent-tagged-post")){
					$(this).parent().parent().find('.tagged-post').show();
				}
				if($(this).hasClass("pop-cat-post") || $(this).hasClass("recent-cat-post")){
					$(this).parent().parent().find('.categories-post').show();
				}
			})
			
			$(".mpl-categories").click(function(){
				var categories = new Array();
				$(this).parent().find(".mpl-categories").each(function(){
					if($(this).is(":checked")){
						var category_value = $(this).val();
						categories.push(category_value);
					}
				})
				
				$(this).parent().find(".mpl-selected-categories").val(categories);
				console.log(categories);
			})
		})
		</script>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['title'] = (!empty($new_instance['title']))?strip_tags($new_instance['title']):'';
		
		$instance['rows'] = (!empty($new_instance['rows']))?$new_instance['rows']:5;
		
		$instance[ 'withImage' ] = $new_instance[ 'withImage' ]?"on":"";
		$instance[ 'withText' ] = $new_instance[ 'withText' ]?"on":"";
		$instance[ 'showDate' ] = $new_instance[ 'showDate' ]?"on":"";
		$instance[ 'withCategory' ] = $new_instance[ 'withCategory' ]?"on":"";
		$instance[ 'includePages' ] = $new_instance[ 'includePages' ]?"on":"";
		
		//popular and recent feautured post
		$instance['filters'] = ( ! empty( $new_instance['filters'] ) ) ? strip_tags( $new_instance['filters'] ) : "unfiltered";
		
		$filters = $instance['filters'];
		if($filters == "popular_tagged" OR $filters == "recent_tagged"){
			$instance['tag_id'] = ( ! empty( $new_instance['tag_id'] ) ) ? strip_tags( $new_instance['tag_id'] ) : "";
		}
		
		if($filters == "popular_cat" OR $filters == "recent_cat"){
			$instance['categories'] =  ( ! empty( $new_instance['selected_categories'] ) ) ? explode( ",",$new_instance['selected_categories'] ) : '';
		}
		
		$instance['text'] = $new_instance['text'];
		$instance['mplTheme'] = $new_instance['mplTheme'];
		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'manage_popularity_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

function get_excerpt_by_id($post_id){
    $the_post = get_post($post_id); //Gets post ID
    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $excerpt_length = 35; //Sets excerpt length by word count
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if(count($words) > $excerpt_length) :
        array_pop($words);
        array_push($words, '…');
        $the_excerpt = implode(' ', $words);
    endif;

    $the_excerpt = '<p>' . $the_excerpt . '</p>';

    return $the_excerpt;
}

function display_results($instance,$post)
{
	$theTheme = $instance["mplTheme"] ;
	$link = get_permalink($post->ID);
	$categories = get_the_category($post->ID);
	$views = get_post_meta($post->ID,"mp_views",true);
	
	$image = ($instance['withImage'] == 'on')?"<img src = '".get_the_post_thumbnail_url( $post->ID )."' alt = '".$post->post_title."' class = 'mpl-featured-image cover-fit-img'/>":"";
	
	$excerpt = ($instance['withText'] == 'on')?get_excerpt_by_id($post->ID):"";
	$date = ($instance['showDate'] == 'on')?"<span class = 'glyphicon glyphicon-time'></span>
		<span>".get_the_date("",$post->ID)."<span>":"";
		
	$category = ($instance['withCategory']=='on')?esc_html( $categories[0]->name ):"";
	
	$themeClass =  $instance["mplTheme"] =="theme4_1"?"theme4 mpl-theme4-1":$instance["mplTheme"];
	$theTheme = $instance["mplTheme"] =="theme4_1"?"theme4":$instance["mplTheme"];
	echo "<li class = 'mpl-".$themeClass." ".$class." '>";
	switch($theTheme){
		case "theme1":
			echo "
			<a href = '".$link."' class = 'hoverBorder pull-left mpl-image-link'>".$image."</a>
			<h4 class = 'title mpl-title'>
				<a href = '".$link."' itemprop='headline'>".$post->post_title."</a>
			</h4>
			<div class = 'mpl-date'>".$date."</div>
			<div class = 'is-separator clearfix' ></div>
			";
		break;
		
		case "theme2":
			echo "<div class = 'col-md-6 col-sm-12 left-container'>
				<h3 class = 'title mpl-title'>
					<a href = '".$link."' itemprop='headline'>".$post->post_title."</a>
				</h3>
				<h4 class = 'mpl-text'>".$excerpt."</h4>
			</div>
			<div  class = 'col-md-6 col-sm-12'>
				<div class = 'mpl-container'>
					<a href = '".$link."' itemprop='url' class = 'mpl-image-link'>
						".$image."
						<h3 class = 'mpl-category mpl-box'><span>".strtoupper($category)."</span></h3>
					</a>
				</div>
			</div>
			<div class = 'clearfix' ></div>
			";
		break;
		
		case "theme3":
			echo "<div class = 'col-md-3 col-sm-4 col-xs-6 mpl-column'>
				<div class = 'mpl-container'>
					<a href = '".$link."' class = 'mpl-image-link'>
						".$image."
						<h3 class = 'mpl-title mpl-box'><span>".$post->post_title."</span></h3>
					</a>
				</div>
			</div>
			";
		break;
		
		case "theme4":
			echo "
			<a href = '".$link."' class = 'hoverBorder pull-left mpl-image-link'>".$image."</a>
			<h4 class = 'title mpl-title'>
				<a href = '".$link."' itemprop='headline'>".$post->post_title."</a>
			</h4>
			<div class = 'mpl-text'>
				<p>".$excerpt."</p>
			</div>
			<div class = 'mpl-date'>".$date."</div>
			<div class = 'is-separator clearfix' ></div>
			";
		break;
		
		case "theme5":
			echo "
			<a href = '".$link."' class = 'mpl-image-link'>".$image."</a>
			<a href = '".$link."' class = 'mpl-title'>".$post->post_title."<a/>
			<div class = 'mpl-text'>".$excerpt."</div>
			";
		break;
	}
	echo "</li>";
}