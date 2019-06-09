<?php 
class Auth {
	public static $session_key = 'admin_user_id';
	public static $db_class = 'User';
	public static $roles = array(
		'superadmin' => 'all',
		'admin' => array(
			'create_publisher',
			'delete_publisher', 

			'create_posts',
			'delete_posts',
			'delete_own_posts',

			'edit_posts', 
			'edit_own_posts',

			'edit_comments',
			'edit_own_comments',

			'change_siteconfig', 
			'change_theme', 
			'delete_cache', 
		),
		'publisher' => array(
			'create_posts',
			'delete_own_posts',
			'edit_own_posts',

			'edit_own_comments',
		)
	);
	public static $user;
	public static function guest() {
		return ! static::logged_in();
	}
	public static function logged_in() {
		return isset($_SESSION[static::$session_key]);
	}
	public static function user() {
		if( ! isset(static::$user) ) {
			static::$user = call_user_func(array(static::$db_class, 'get'), $_SESSION[static::$session_key]);
		}
		return static::$user;
	}
	public static function roles() {
		return static::$roles;
	}
	public static function userCan($action) {
		$current_user_actions = static::$roles[static::user()->role];
		if( $current_user_actions === 'all' ) {
			return true;
		}
		return in_array($action, $current_user_actions);
	}
}