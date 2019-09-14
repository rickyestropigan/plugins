<?php
/**
 * Plugin Name: URL Shortener
 * Description: A URL Shortener
 * Version: 0.0.1
 * Author: Ricky Estropigan
 */ 


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'RME_TEMPLATEPATH', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) . '/views/templates/' );
define( 'RME_PATH', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) );
//require_once( plugin_dir_path( __FILE__ ) . 'rme-link/rme-markup.php' );
require_once( plugin_dir_path( __FILE__ ) . 'rme-link/rme-function.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/rme-links.php' );

wp_enqueue_script('rme-scripts', RME_PATH . 'admin/js/rme-js.js',array(), '1.0.0', true );
wp_enqueue_style('rme-style', RME_PATH . 'admin/css/style.css');
wp_enqueue_style('thickbox');
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');

register_activation_hook( __FILE__, array( 'RME_links', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'RME_links', 'deactivate' ) );
add_action( 'plugins_loaded', array( 'RME_links', 'get_instance' ) );


?>