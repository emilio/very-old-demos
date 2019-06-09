<?php
/**
 * @author Emilio Cobos
 */
class Google_Feeds {
	/** La url base a la que pedir los feeds */
	public static $baseurl = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&';

	/**
	 * Obtener un feed
	 * @param string $url la url al feed
	 * @param string $results el número de resultados del feed
	 * @return string el json de la url base
	 */
	public static function get($url, $results = 5) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$url = static::$baseurl . http_build_query(array(
			'userip' => $ip,
			'q' => $url,
			'num' => $results
		));
		
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $result = curl_exec($ch);
		// curl_close($ch);
		$result = file_get_contents($url);
		return json_decode($result);
	}
}