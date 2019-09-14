<?php
/**
 * Plugin Name: TheBitHub Gamification 
 * Plugin URI: 
 * Description: TheBitHub plugin
 * Author: The PenBach plugin - Credit to Ricky Esptropigan
 * Version: 1.7.7
 * License: GPLv2 or later
 */
 
	defined('ABSPATH') or die("No script kiddies please!");
 
	global $woocommerce, $wp_list_table;

	include 'autoload.php'; //if the directory need load
	
	include 'libs/libs.function.php'; //this a external function
	include 'libs/libs.global.php'; //global variable to be declaire like array or string
	
	//includes
 	include 'includes/class.rank.php';
	
 	include 'gimification.php';
	include 'gami_settings.php';
	include 'gami_levelling.php';
	include 'gami_badge.php';

	$GIMIFICATION = new GIMIFICATION();
	$GAMI_SETTINGS = new gami_settings();
	$GAMI_LEVELLING = new gami_levelling();
	$GAMI_BADGE = new gami_badge();
	
?>