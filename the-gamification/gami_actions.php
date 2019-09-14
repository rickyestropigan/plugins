<?php
include_once 'gimification.php';


class gami_actions extends GIMIFICATION  {
	
	public function __construct(){
		add_action('wp_login', array(&$this, 'gami_actions_login'));
	}
	
	public function gami_actions_login() {
		global $gimi_options;

		$gami_val['qty'] = '1';
		$gami_val['points_earn'] = $gimi_options['action_login_points'];
		$gami_val['from_action_type'] = 'login';
		$gami_val['order_id'] = ''; //null

		self::gami_actions($gami_val);	
		
	}

}
?>