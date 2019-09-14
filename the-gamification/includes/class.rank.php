<?php
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Link_List_Table extends WP_List_Table {

   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    function __construct() {
       parent::__construct( array(
      'singular'=> 'wp_list_text_link', //Singular label
      'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
    }
/**
 * Add extra markup in the toolbars before or after the list
 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
 */
	function extra_tablenav( $which ) {
	   if ( $which == "top" ){
		  //The code that goes before the table is here
		  echo"<h1>Gamification User Rank Listing</h1>";
	   }
	   if ( $which == "bottom" ){
		  //The code that goes after the table is there
		  echo"";
	   }
	}
	
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
	   return $columns= array(
		  'col_id'=>__('ID'),
		  'col_name'=>__('Name'),
		  'col_email'=>__('Email'),
		  'col_gami_points'=>__('Gami Points'),
		  'col_gami_level'=>__('Level'),
		  'col_badge'=>__('Badge')
	   );
	}	
	
	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
	   return $sortable = array(
		  'col_id'=>'link_id',
		  'col_name'=>'link_name',
		  'col_gami_points'=>'link_visible'
	   );
	}	
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
	   global $wpdb, $_wp_column_headers;
	   $screen = get_current_screen();

	   /* -- Preparing your query -- */
			$query = "SELECT * FROM $wpdb->users";

	   /* -- Ordering parameters -- */
		   //Parameters that are going to be used to order the result
		   $orderby = !empty($_GET["user_login"]) ? mysql_real_escape_string($_GET["user_login"]) : 'ASC';
		   $order = !empty($_GET["ID"]) ? mysql_real_escape_string($_GET["ID"]) : '';
		   if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

	   /* -- Pagination parameters -- */
			//Number of elements in your table?
			$totalitems = $wpdb->query($query); //return the total number of affected rows
			//How many to display per page?
			$perpage = 20;
			//Which page is this?
			$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
			//Page Number
			if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; } 
			// How many pages do we have in total? 
			$totalpages = ceil($totalitems/$perpage); 
			//adjust the query to take pagination into account F
			
			if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; } 
			/* -- Register the pagination -- */ 
			$this->set_pagination_args( array(
			  "total_items" => $totalitems,
			 "total_pages" => $totalpages,
			  "per_page" => $perpage,
		   ) );
		  //The pagination links are automatically built according to those parameters

	   /* -- Register the Columns -- */

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		/* echo "<pre>";
	print_r($wpdb->get_results($query));

		echo "</pre>";	 */
		  $this->items = $wpdb->get_results($query);
		  
	}	
	
	function column_col_id($item){
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&rank=%s">Edit</a>',$_REQUEST['page'],'edit',$item->ID),
			'delete'    => sprintf('<a href="?page=%s&action=%s&rank=%s">Delete</a>',$_REQUEST['page'],'delete',$item->ID),
		);

		//Return the title contents
		return sprintf('<div>%1$s</div> %2$s',
			/*$0%s*/ 'img',
			/*$3%s*/ $this->row_actions($actions)
		);
	}

	function column_col_name($item){
		//Return the title contents
		return $item->user_login;
	}

	function column_col_email($item){
		//Return the title contents
		return $item->user_email;
	}
	function column_col_gami_points($item){
		//Return the title contents
		//return var_dump(get_post_meta($item->ID,'gami_points'));
		$args = array(
			'author'        =>  $item->ID,
			'post_type'       =>  'gamification',
			'orderby'       =>  'post_date',
			'order'         =>  'ASC',
			'post_status'      => 'private'
		);
		$cpoint = 0;
		$gami_post = get_posts( $args );
		foreach ( $gami_post as $post ) : setup_postdata( $post ); 
			$key_1_value = get_post_meta( $post->ID, 'gami_points',true );
			$cpoint = $cpoint + $key_1_value;
			//print_r($key_1_value);
		endforeach; 

		
		return $cpoint;
	}

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	 /* 
	function display_rows() {

	   //Get the records registered in the prepare_items method
	   $users = $this->items;
		//print_r($users);
	   //Get the columns registered in the get_columns and get_sortable_columns methods
	   
	  
	   list( $columns, $hidden ) = $this->get_column_info();
 //print_r($columns);
	   //Loop for each record
	   if(!empty($users)){foreach($users as $user){

		  //Open the line
			echo '< tr id="record_'.$user->ID.'">';
		  foreach ( $columns as $column_name => $column_display_name ) {

			 //Style attributes for each col
			 $class = "class='$column_name column-$column_name'";
			 $style = "";
			 if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
			 $attributes = $class . $style;

			 //edit link
			 $editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$user->ID;
			echo $column_name;
			 //Display the cell
			 switch ( $column_name ) {
				case "col_id":  echo '< td '.$attributes.'>'.stripslashes($user->ID).'< /td>';   break;
				case "col_name": echo '< td '.$attributes.'>sdfasd< /td>'; break;
				case "col_email": echo '< td '.$attributes.'>asdfas< /td>'; break;
				case "col_gami_points": echo '< td '.$attributes.'>asdfa< /td>'; break;
				case "col_gami_level": echo '< td '.$attributes.'>sdfas< /td>'; break;
				case "col_badge": echo '< td '.$attributes.'>asdfa< /td>'; break;
			 }
		  }
		  
		  
		  //Close the line
		  echo'< /tr>';
	   }}
	} */
	
}

?>