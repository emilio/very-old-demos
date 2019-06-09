<?php namespace Sources\Google;
use Curl;

class Plus extends \Sources\Google\API {
	public static $base_url = 'https://www.googleapis.com/plus/v1/people';
	public static function search($term) {
		$url = self::get_url(array(
			'query' => self::get_query($term)
		));
		$result = json_decode(Curl::get($url));

		if( isset($result->items) && count($result->items) ) {
			return $result;
		}

		return null;
	}
}