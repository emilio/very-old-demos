<?php
/**
 * Main API class for data managing
 */
class API {
	/** Extensión de los archivos a guardar */
	public static $extension = '.php';
	/**
	 * Transfrorms a string into a uri
	 * @param string $str
	 */
	public static function transform_string($str) {
		/**
		 * @todo ¿escape utf8?
		 */
		return str_replace(' ', '-', $str);
	}

	/**
	 * Get the filename corresponding to a search
	 * @param string $search the search term
	 */
	public static function get_file($search) {
		return self::get_file_from_path(self::transform_string($search));
	}

	/**
	 * Get the filename corresponding to a path in the url
	 * @param string $path
	 */
	public static function get_file_from_path($path) {
		return Config::get('path.searches') . DS . $path . self::$extension;
	}

	/**
	 * Get relevant data for a term
	 * @param string $search the search term
	 */
	public static function get($search) {
		$file = self::get_file($search);
		if( file_exists($file) && filemtime($file) > (time() - SEARCHES_EXPIRATION_TIME))  {
			return json_decode(file_get_contents($file));
		}

		$data = self::search($search);

		self::minify($data);

		file_put_contents($file, json_encode($data));

		return $data;
	}
	/**
	 * Get relevant data for a term, creating a registry in the table
	 * @param string $search the search term
	 */
	public static function create($search) {
		$data = API::get($search);
		Search::create(array(
			'path' => static::transform_string($search),
			'formatted_term' => ucwords($search)
		));
	}

	/**
	 * Get info
	 * @param string $search
	 */
	public static function search($search) {
		$return = array();

		$return['data'] = array(
			'originalSearch' => $search,
			'lastUpdated' => time(),
		);

		$return['wikipedia'] = \Sources\WikiPedia::search($search);
		$return['googleblogs'] = \Sources\Google\Blogs::search($search);
		$return['googleplus'] = \Sources\Google\Plus::search($search);
		$return['googleimages'] = \Sources\Google\Images::search($search);
		$return['googlenews'] = \Sources\Google\News::search($search);
		$return['youtube'] = \Sources\Google\YouTube::search($search);
		$return['twitter'] = \Sources\Twitter::search($search);
		$return['facebook'] = \Sources\Facebook::search($search);

		return $return;
	}

	/**
	 * Minify information which is going to be stored
	 */
	public static function minify(&$data) {
		if( $data['wikipedia'] !== null ) {
			$data['wikipedia'] = $data['wikipedia']->query;
		}
		if( $data['googleblogs'] !== null ) {
			$data['googleblogs'] = $data['googleblogs']->responseData;
			unset($data['googleblogs']->cursor);
		}
		if( $data['googleimages'] !== null ) {
			$data['googleimages'] = $data['googleimages']->responseData;
			unset($data['googleimages']->cursor);
		}
		if( $data['googlenews'] !== null ) {
			$data['googlenews'] = $data['googlenews']->responseData;
			unset($data['googlenews']->cursor);
			foreach ($data['googlenews']->results as &$result) {
				if( isset($result->relatedStories) ) {
					unset($result->relatedStories);
				}
			}
		}
		if( $data['googleplus'] !== null ) {
			$data['googleplus'] = (object) array(
				'items' => $data['googleplus']->items
			);
		}
		if( $data['youtube'] !== null ) {
			$data['youtube'] = (object) array(
				'items' => $data['youtube']->items
			);
		}
		if( $data['twitter'] !== null ) {
			/** Borrar todo lo que no nos interesa mostrar */
			foreach ($data['twitter']->statuses as &$status) {
				unset($status->source);
				unset($status->truncated);
				unset($status->in_reply_to_status_id);
				unset($status->in_reply_to_status_id_str);
				unset($status->in_reply_to_user_id);
				unset($status->in_reply_to_user_id_str);
				unset($status->in_reply_to_screen_name);
				unset($status->geo);
				unset($status->coordinates);
				unset($status->place);
				unset($status->contributors);
				unset($status->retweet_count);
				unset($status->favorite_count);
				unset($status->favorited);
				unset($status->retweeted);
				unset($status->lang);
				if( isset($status->retweeted_status) ) {
					unset($status->retweeted_status);
				}


				unset($status->user->followers_count);
				unset($status->user->friends_count);
				unset($status->user->listed_count);
				unset($status->user->created_at);
				unset($status->user->favourites_count);
				unset($status->user->utc_offset);
				unset($status->user->time_zone);
				unset($status->user->geo_enabled);
				unset($status->user->verified);
				unset($status->user->statuses_count);
				unset($status->user->lang);
				unset($status->user->contributors_enabled);
				unset($status->user->is_translator);
				unset($status->user->profile_background_color);
				unset($status->user->profile_background_image_url);
				unset($status->user->profile_background_image_url_https);
				unset($status->user->profile_background_tile);
				unset($status->user->profile_image_url_https);
				unset($status->user->profile_banner_url);
				unset($status->user->profile_link_color);
				unset($status->user->profile_sidebar_border_color);
				unset($status->user->profile_sidebar_fill_color);
				unset($status->user->profile_text_color);
				unset($status->user->profile_use_background_image);
				unset($status->user->default_profile);
				unset($status->user->default_profile_image);
				unset($status->user->following);
				unset($status->user->follow_request_sent);
				unset($status->user->notifications);
				if (isset($status->user->entities)) {
					unset($status->user->entities);
				}
			}
		}
		if( $data['facebook'] !== null ) {
			// Borrar lo que no nos interesa, y reducir el mensaje si es muy largo
			foreach ($data['facebook']->data as &$post) {
				unset($post->privacy);
				unset($post->shares);
				unset($post->likes);
				if( isset($post->to) ) {
					unset($post->to);
				}
				if( isset($post->message) ) {
					$post->message = substr($post->message, 0, 150);	
				}
			}
		}

		// Igualdad entre json_decode y esta obtención directa
		$data = (object) $data;
		// return $data;
	}
}