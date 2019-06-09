<?php namespace Sources;
use Curl;
class WikiPedia {
	public static $base_url = 'http://es.wikipedia.org/w/api.php';
	public static function search($term) {
		$search = Curl::get(self::$base_url,
			array(
				'format' => 'json',
				'action' => 'query',
				'list' => 'search',
				'srsearch' => $term,
				'srprop' => 'snippet',
				'srlimit' => 5
			),
			array(
				CURLOPT_USERAGENT => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'EC-WikiAPI-SDK-1'
			)
		);

		$result = json_decode($search);

		if( isset($result->query->search) && count($result->query->search) ) {
			return $result;
		}

		return null;
	}
}