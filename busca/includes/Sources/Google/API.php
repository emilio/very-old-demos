<?php namespace Sources\Google;
class API {
	public static $base_url;
	public static $key = '';
	public static $max_results = 5;
	public static function get_query($term) {
		return str_replace(' ', '+', $term);
	}
	public static function get_url($params = array()) {
		return static::$base_url . '?' . http_build_query(array_merge(array(
			'key' => static::$key,
			'maxResults' => static::$max_results,
		), $params));
	}
}
