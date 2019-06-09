<?php namespace Sources\Google;
use Curl;

class YouTube extends \Sources\Google\API {
	public static $base_url = 'https://www.googleapis.com/youtube/v3/search';
	public static function search($term) {
		$url = self::get_url(array(
			'q' => self::get_query($term),
			'part' => 'snippet',
		));
		$result = json_decode(Curl::get($url));

		if( isset($result->items) && count($result->items) ) {
			return $result;
		}

		return null;
	}
}