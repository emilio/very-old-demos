<?php namespace Sources\Google;
use Curl;

class Blogs extends \Sources\Google\API {
	public static $base_url = 'https://ajax.googleapis.com/ajax/services/search/blogs';
	public static function search($term) {
		$url = self::get_url(array(
			'v' => '1.0',
			'q' => self::get_query($term),
			'userip' => USER_IP,
			'hl' => 'es',
			'rsz' => 5,
		));

		$result = json_decode(Curl::get($url));
		if (isset($result->responseData) && isset($result->responseData->results) && count($result->responseData->results)) {
			return $result;
		}

		return null;
	}
}