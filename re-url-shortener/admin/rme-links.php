<?php
class RME_links {

	public $VERSION = '0.0.1';
	protected $plugin_slug = 'rme_links';
	protected static $instance = null;
	private $custom_meta_prefix = 'rme_links_custom_';
	private $settings_name = 'rme_links_settings';
	private static $default_settings = null;

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ),0 );
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
		add_action( 'init', array( $this, 'create_rme_links' ), 0 );
		add_action( 'wp_ajax_do_scraper', 'do_scraper' );
		add_action( 'wp_ajax_nopriv_do_scraper', 'do_scraper' );
        add_action( 'init', array( $this, 'url_shortener_db' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_image_meta_box' ) );
		add_action( 'init', array( $this, 'plugin_init' ), 0 );
		add_action( 'save_post', array( $this, 'save_custom_meta' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 0 );
		add_filter( 'post_row_actions', array( $this, 'remove_actions'), 0 );
	}
	
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}
	
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}
	
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}
	
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}
		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}
	
	private static function get_blog_ids() {
		/** @var $wpdb WPDB */
		global $wpdb;
		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}
	
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}
	
	public function url_shortener_db() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$tblog = $wpdb->prefix . 'rme_og_tags';
		$tbllinks = $wpdb->prefix . 'rme_links';
		$tblclicks = $wpdb->prefix . 'rme_clicks';
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS {$tblog}(
			urlid int(11),
			urlsitename varchar(255),
			urlurl varchar(255),
			urltitle varchar(255),
			urlimg varchar(255),
			urldesc varchar(350),
			urlredpixel text,
			urlredirect varchar(10),
			urltype varchar(255),
			urlshorten varchar(255),
			mTitle varchar(255),
			mArtist varchar(255),
			mAlbum varchar(255),
			mRelease varchar(255),
			vUrl varchar(255),
			vSUrl varchar(255),
			vType varchar(255),
			vWidth varchar(255),
			vHeight varchar(255),
			aPubTime varchar(255),
			aModTime varchar(255),
			aExpTime varchar(255),
			aAuthor varchar(255),
			aSection varchar(255),
			bAuthor varchar(255),
			bISBN varchar(255),
			bRelease varchar(255),
			pFName varchar(255),
			pLName varchar(255),
			pUsername varchar(255),
			pGender varchar(255)
		)$charset_collate;";
		dbDelta( $sql );		

		$sql = "CREATE TABLE IF NOT EXISTS {$tblclicks}(
			id int(11),
			ip varchar(255) default NULL,
			browser varchar(255) default NULL,
			btype varchar(255) default NULL,
			bversion varchar(255) default NULL,
			os varchar(255) default NULL,
			referer varchar(255) default NULL,
			host varchar(255) default NULL,
			uri varchar(255) default NULL,
			robot tinyint default 0,
			first_click tinyint default 0,
			created_at datetime NOT NULL,
			link_id int(11) default NULL,
			vuid varchar(25) default NULL
		)$charset_collate;";
		dbDelta( $sql );
		add_option( 'url_shortener_version', $VERSION );
	}

	public function plugin_init() {
		$this->custom_meta_fields = array(
			array(
				'label' => 'URL *',
				'desc'  => 'Once this is changed, it will automatically scraped.',
				'id'    => $this->custom_meta_prefix . 'campaign_url',
				'type'  => 'text',
				'class' => 'required'
			),
			array(
				'label' => 'Title *',
				'desc'  => 'This field is required.',
				'id'    => $this->custom_meta_prefix . 'campaign_title',
				'type'  => 'text',
				'class' => 'required'
			),
			array(
				'label' => 'Description *',
				'desc'  => 'This field is required.',
				'id'    => $this->custom_meta_prefix . 'campaign_description',
				'type'  => 'textarea',
				'class' => 'required'
			),

			array(
				'label' => 'Retargeting pixel, Facebook meta data, and google analytics',
				'desc'  => '',
				'id'    => $this->custom_meta_prefix . 'header_code',
				'type'  => 'textarea',
				'class' => 'header_code'
			),
			array(
				'label' => 'Redirection Type *',
				'desc'  => 'This field is required',
				'id'    => $this->custom_meta_prefix . 'redirection_type',
				'type'  => 'select',
				'options' => array( 
								array('label'=> '307','value' => '307'),
								array('label'=> '301','value' => '301')
								),
				'class' => 'redirection_type'
			),
			array(
				'label' => 'Type *',
				'desc'  => 'This field is required',
				'id'    => $this->custom_meta_prefix . 'type',
				'type'  => 'select',
				'options' => array( 
								array('label'=> '----','value' => ''),
								array('label'=> 'Site','value' => 'site'),
								array('label'=> 'Music','value' => 'music'),
								array('label'=> 'Video','value' => 'video'),
								array('label'=> 'Article','value' => 'article'),
								array('label'=> 'Book','value' => 'book'),
								array('label'=> 'Profile','value' => 'profile')
								),
				'class' => 'type'
			),
			array(
				'label' => 'Published Time',
				'desc'  => 'Article Published Time',
				'id'    => $this->custom_meta_prefix . 'campaign_article',
				'type'  => 'text',
				'class' => 'article required'
			),
			array(
				'label' => 'Modified Time',
				'desc'  => 'Article Modified Time',
				'id'    => $this->custom_meta_prefix . 'campaign_modtime',
				'type'  => 'text',
				'class' => 'article required'
			),
			array(
				'label' => 'Expiration Time',
				'desc'  => 'Article Expiration Time',
				'id'    => $this->custom_meta_prefix . 'campaign_exptime',
				'type'  => 'text',
				'class' => 'article required'
			),
			array(
				'label' => 'Author',
				'desc'  => 'Author Name',
				'id'    => $this->custom_meta_prefix . 'campaign_author',
				'type'  => 'text',
				'class' => 'book required'
			),
			array(
				'label' => 'ISBN',
				'desc'  => 'ISBN',
				'id'    => $this->custom_meta_prefix . 'campaign_isbn',
				'type'  => 'text',
				'class' => 'book required'
			),
			array(
				'label' => 'Release Date',
				'desc'  => 'Release Date',
				'id'    => $this->custom_meta_prefix . 'campaign_releasedate',
				'type'  => 'text',
				'class' => 'book required'
			),
			array(
				'label' => 'First Name',
				'desc'  => 'First Name',
				'id'    => $this->custom_meta_prefix . 'campaign_fn',
				'type'  => 'text',
				'class' => 'profile required'
			),
			array(
				'label' => 'Last Name',
				'desc'  => 'Last Name',
				'id'    => $this->custom_meta_prefix . 'campaign_ln',
				'type'  => 'text',
				'class' => 'profile required'
			),
			array(
				'label' => 'Username',
				'desc'  => 'Username',
				'id'    => $this->custom_meta_prefix . 'campaign_un',
				'type'  => 'text',
				'class' => 'profile required'
			),
			array(
				'label' => 'Gender',
				'desc'  => 'Gender',
				'id'    => $this->custom_meta_prefix . 'campaign_g',
				'type'  => 'text',
				'class' => 'profile required'
			),
			array(
				'label' => 'Video URL',
				'desc'  => 'Video URL',
				'id'    => $this->custom_meta_prefix . 'campaign_vu',
				'type'  => 'text',
				'class' => 'video required'
			),
			array(
				'label' => 'Video Secure URL',
				'desc'  => 'Video Secure URL',
				'id'    => $this->custom_meta_prefix . 'campaign_vsu',
				'type'  => 'text',
				'class' => 'video required'
			),
			array(
				'label' => 'Video Type',
				'desc'  => 'Video Type',
				'id'    => $this->custom_meta_prefix . 'campaign_vt',
				'type'  => 'text',
				'class' => 'video required'
			),
			array(
				'label' => 'Video Width',
				'desc'  => 'Video Width',
				'id'    => $this->custom_meta_prefix . 'campaign_vw',
				'type'  => 'text',
				'class' => 'video required'
			),
			array(
				'label' => 'Video Height',
				'desc'  => 'Video Height',
				'id'    => $this->custom_meta_prefix . 'campaign_vh',
				'type'  => 'text',
				'class' => 'video required'
			),
			array(
				'label' => 'Music Title',
				'desc'  => 'Music Title',
				'id'    => $this->custom_meta_prefix . 'campaign_mt',
				'type'  => 'text',
				'class' => 'music required'
			),
			array(
				'label' => 'Artist',
				'desc'  => 'Artist',
				'id'    => $this->custom_meta_prefix . 'campaign_artist',
				'type'  => 'text',
				'class' => 'music required'
			),
			array(
				'label' => 'Album',
				'desc'  => 'Album',
				'id'    => $this->custom_meta_prefix . 'campaign_album',
				'type'  => 'text',
				'class' => 'music required'
			),
			array(
				'label' => 'Release Date',
				'desc'  => 'Release Date',
				'id'    => $this->custom_meta_prefix . 'campaign_rd',
				'type'  => 'text',
				'class' => 'music required'
			)
		);
	}

	private function _get_settings( $settings_key = null ) {
		$defaults = $this->_get_settings_default();
		$settings = get_option( $this->settings_name, $defaults );
		$settings = shortcode_atts( $defaults, $settings );
		return is_null( $settings_key ) ? $settings : ( isset( $settings[$settings_key] ) ? $settings[$settings_key] : false );
	}

	private static function _get_settings_default() {
		if ( is_null( self::$default_settings ) ) {
			self::$default_settings = array(
				'header-code' => '',
				'footer-code' => '',
			);
		}
		return self::$default_settings;
	}
	 
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), $this->VERSION );

	}

	 public function add_image_meta_box() {

		add_meta_box(
			'image_meta_box', // $id
			'URL Image', // $title
			array( $this, 'show_image_meta_box' ), // $callback
			'rme_links', // $page
			'side', // $context $position
			'default' ); // $priority
	}

	public function add_custom_meta_box() {

		add_meta_box(
			'custom_meta_box', // $id
			'URL Shortener', // $title
			array( $this, 'show_custom_meta_box' ), // $callback
			'rme_links', // $page
			'normal', // $context $position
			'high' ); // $priority

	}

	

	

	public function remove_actions( $actions ){

		if( get_post_type() === 'rme_links' )
			unset( $actions['view'] );
			unset( $actions['inline hide-if-no-js'] );
		return $actions;

	}

	

	public function show_image_meta_box() {

		global $post;
		/* add_thickbox();  */
?>
			<div id="minor-publishing-actions">
				<input style="width:100% !important;" id="upload_image" type="text" size="36" name="scrape_image" value="<?php echo get_post_meta( $post->ID, "scrape_image", true );?>" />
				<input  style="margin:5px 0px" id="upload_image_button" class="button button-primary button-large" type="button" value="Upload Image" />
				<br />Enter an URL or upload an image for the banner.
			</div>'
<?php
	}

	public function show_custom_meta_box() {

		global $post;
		
		echo '
		<div id="loader">
			<div id="facebookG">
				<div id="blockG_1" class="facebook_blockG">
				</div>
				<div id="blockG_2" class="facebook_blockG">
				</div>
				<div id="blockG_3" class="facebook_blockG">
				</div>
			</div>
			<div class="clear"></div>
		</div>';
		
		echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

		echo '<table class="form-table">';
		foreach ( $this->custom_meta_fields as $field ) {

			$meta = get_post_meta( $post->ID, $field['id'], true );
			echo '<tr><th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th><td>';
			switch ( $field['type'] ) {
				//text
				case 'text':
					echo '<input class="' . $field['class'] . '" type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="60" />
                        <br /><span class="description">' . $field['desc'] . '</span>';
					break;
				//textarea
				case 'textarea':
					echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>
                        <br /><span class="description">' . $field['desc'] . '</span>';
					break;
				//checkbox
				case 'checkbox':
					echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '', '/>
                        <label for="' . $field['id'] . '">' . $field['desc'] . '</label>';
					break;
				//select
				case 'select':
					echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
 					foreach ( $field['options'] as $option ) {
						echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
					} 
					echo '</select><br /><span class="description">' . $field['desc'] . '</span>';
					break;
				//date
				case 'date':
					echo '<input type="text" class="datepicker" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30" />
			            <br /><span class="description">' . $field['desc'] . '</span>';
					break;
				//image
				case 'image':
					$image = get_template_directory_uri() . '/images/image.png';
					echo '<span class="custom_default_image" style="display:none">' . $image . '</span>';
					if ( $meta ) {
						$image = wp_get_attachment_image_src( $meta, 'medium' );
						$image = $image[0];
					}
					echo '<input name="' . $field['id'] . '" type="hidden" class="custom_upload_image" value="' . $meta . '" />
                        <img src="' . $image . '" class="custom_preview_image" alt="" /><br />
                    <input class="custom_upload_image_button button" type="button" value="Choose Image" />
                    <small> <a href="#" class="custom_clear_image_button">Remove Image</a></small>';
					break;
			}
			echo '</td></tr>';
		}
		echo '</table>';
	  }

		public function save_custom_meta( $post_id ) {
			if ( isset( $_POST['custom_meta_box_nonce'] ) ) {
				$nonce = $_POST['custom_meta_box_nonce'];
			} else {
				$nonce = false;
			}
			if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
				return $post_id;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
			foreach ( $this->custom_meta_fields as $field ) {
				$old = get_post_meta( $post_id, $field['id'], true );
				$new = $_POST[$field['id']];
				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			}
		update_post_meta( $post_id, 'scrape_image', $_POST['scrape_image']);
	}

	public function create_rme_links() {
		register_post_type( 'rme_links',
			array(
				'labels' => array(
					'name' => 'Shorten Links',
					'singular_name' => 'Shorten Link',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Shorten Link',
					'edit' => 'Edit',
					'edit_item' => 'Edit Shorten Link',
					'new_item' => 'New Shorten Link',
					'view' => 'View',
					'view_item' => 'View Shorten Link',
					'search_items' => 'Search Shorten Links',
					'not_found' => 'No Shorten Links found',
					'not_found_in_trash' => 'No Shorten Links found in Trash',
					'parent' => 'Parent Shorten Link'
				),
				'public' => true,
				'menu_position' => 15,
				'supports' => array( 'title', 'comments'),
				'taxonomies' => array( '' ),
				'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
				'has_archive' => true,								'rewrite' => array('slug' => 'rme_links', 'with_front' => false)
			)
		);
	}

	public function include_template_function( $template_path ) {
		if ( get_post_type() == 'rme_links' ) {
			if ( is_single() ) {

				if ( $theme_file = locate_template( array ( 'single-rme_templates.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = plugin_dir_path( __FILE__ ) . 'views/templates/single-rme_templates.php';
				}
			}
		}
		return $template_path;
	}

	function template_redirect() {
			if (get_post_type() == 'rme_links') {
				global $post;
				$settings = $this->_get_settings();
				$campaign_title = get_post_meta($post->ID, $this->custom_meta_prefix.'campaign_title', true);
				$campaign_image = get_post_meta($post->ID, 'scrape_image', true);
				$campaign_url = get_post_meta($post->ID, $this->custom_meta_prefix.'campaign_url', true);
				$redirect_url = get_post_meta($post->ID, $this->custom_meta_prefix.'redirect_url', true);
				$header_code = get_post_meta($post->ID, $this->custom_meta_prefix.'header_code', true);
				$footer_code = get_post_meta($post->ID, $this->custom_meta_prefix.'footer_code', true);

				$redirection_type = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'redirection_type', true );
				$campaign_description = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_description', true );
				$redirection_type = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'redirection_type', true );
				$type = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'type', true );
				$campaign_modtime = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_modtime', true );
				$campaign_exptime = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_exptime', true );
				$campaign_author = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_author', true );
				$campaign_isbn = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_isbn', true );
				$campaign_releasedate = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_releasedate', true );
				$campaign_fn = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_fn', true );
				$campaign_ln = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_ln', true );
				$campaign_un = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_un', true );
				$campaign_g = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_g', true );
				$campaign_vu = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_vu', true );
				$campaign_vsu = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_vsu', true );
				$campaign_vt = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_vt', true );
				$campaign_vw = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_vw', true );
				$campaign_vh = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_vh', true );
				$campaign_mt = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_mt', true );
				$campaign_artist = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_artist', true );
				$campaign_album = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_album', true );
				$campaign_rd = get_post_meta( $post->ID,  $this->custom_meta_prefix . 'campaign_rd', true );

				$popup_message = get_post_meta($post->ID, $this->custom_meta_prefix.'popup_message', true);
				if (!empty($popup_message)) {
					$popup_message = json_encode($popup_message);
				} else {
					$popup_message = "";
				}

				$end_date = get_post_meta($post->ID, $this->custom_meta_prefix.'end_date', true);
				if ( !empty($end_date) && strtotime($end_date)-time() > 0 ) {
					$end_date = date('D M d Y H:i:s O', strtotime($end_date));
				} else {
					$end_date = '';
				}
				$background_image = get_post_meta($post->ID, $this->custom_meta_prefix.'image', true);
				$background_image = wp_get_attachment_url($background_image, true);

				include (RME_TEMPLATEPATH . 'single-rme_templates.php');
				exit;
			}
		}
}