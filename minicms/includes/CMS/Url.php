<?php namespace CMS;

class Url {
	public static function home() {
		return URL;
	}
	public static function to_page($page) {
		if( $page === 0 ) {
			return static::home();
		}
		return URL . 'page/' . $page . '/';
	}
	public static function to_template($route) {
		return TEMPLATE_URL . $route;
	}
	public static function to_post($slug) {
		return URL . $slug . '/';
	}
	public static function to_category($category, $page = 0) {
		$ret = URL . 'category/' . urlencode($category) . '/';
		if( $page > 0 ) {
			$ret .= 'page/' . $page . '/';
		}
		return $ret;
	}
}