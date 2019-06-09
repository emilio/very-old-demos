<?php namespace Sources;
use Curl;
class Facebook {
	public static $app_id = '131496853677500';
	public static $app_secret = 'f3823ba4c50341778338685105f983b3';
	public static $base_url = 'https://graph.facebook.com';
	// Un token de usuario para la app indicada que tenga los permisos manage_pages. Puedes obtenerlo en developers.facebook.com/tools/explorer
	// public static $user_access_token = 'AAAB3mH9ApbwBAAkUjexZA32tO5ENrInZAtetPbRB3qBX5k6VuA9cGhcIfXPLFW2eU5BWAt6s8CAbai3sf2hUBru3aioniDhhmCKBvKTsZBaoiZCXNlQ7';

	// Borrar si hay algún error, generar el de arriba, y listo
	public static $access_token; // = 'AAAB3mH9ApbwBAGYsDEChzp9j9Bp9ZCaLEVaKIi4b2za38Nji1XBzubYVnaQ8LmABhmzZAcahUCmjdcEyvYEmUYklg5s5biRRj3bgZCMjgZDZD';
	public static function search($term, $type = 'post' ) {
		// if( ! isset(self::$access_token) ) {
		// 	$params = null;
		// 	$result = self::api('/oauth/access_token', array(
		// 		'client_id' => self::$app_id,
		// 		'client_secret' => self::$app_secret,
		// 		'grant_type' => 'fb_exchange_token',
		// 		'fb_exchange_token' => self::$user_access_token
		// 	), true);

		// 	$new_access_token = parse_str($result, $params);
		// 	$new_access_token = $params['access_token'];
		// 	die($new_access_token);

			/**
			 * Seguir con el código de abajo si queremos obtener un access_token permanente como página (no parece válido para buscar)
			 */
		// 	$new_token_response = self::api('/me/accounts', array(
		// 		'access_token' => $new_access_token
		// 	));

		// 	if( ! is_object($new_token_response) || isset($new_token_response->error) ) {
		// 		throw new \Exception('Facebook api error: ' . $new_token_response->error->message, 1);
		// 	} elseif (! isset($new_token_response->data) || ! count($new_token_response->data) ) {
		// 		throw new \Exception('Facebook API: No hay páginas para loguearse', 1);
		// 	}

		// 	self::$access_token = $new_token_response->data[0]->access_token;

		// 	die(self::$access_token);
		// }

		$result = self::api('/search', array(
			'q' => $term,
			'type' => $type,
			'access_token' => self::$access_token,
			'limit' => 5
		));

		if( isset($result->data) && count($result->data) ) {
			return $result;			
		}

		return null;
	}

	public static function api($url, $params = array(), $raw = false) {
		$result = Curl::get(self::$base_url . $url, $params);
		return $raw ? $result : json_decode($result);
	}
}