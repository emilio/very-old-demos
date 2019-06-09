<?php namespace Sources;
use Curl,
	Auth\Twitter as TwitterOAuth;
class Twitter {
	public static $auth_instance;

	public static function search($term) {
		if( ! self::$auth_instance ) {
													/* Key, secret, token, token secret */
			self::$auth_instance = new TwitterOAuth('zvTVPY1LgBWXX09MJWOL2Q', 'cuFZlfRDfKYzcJ674e3KwsRhfOL0OKoBKBfLAg', '380370057-QSHCtHM6CBjFqQDyu4MI46JB8TA6VilC2u3H4sgj', 'k5D1ga9GKsDau7RQHtykj3ODISb1BucsAz15JXObIE');
		}

		$result = self::$auth_instance->get('search/tweets', array(
			'q' => $term,
			'lang' => 'es',
			'count' => 5,
			'include_entities' => false,
		));

		if( isset($result->statuses) && count($result->statuses) ) {
			return $result;
		}
		return null;
	}
}