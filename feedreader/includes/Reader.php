<?php
/**
 * @author Emilio Cobos (http://emiliocobos.net)
 */
class Reader {
	/** Todos los datos de los feeds pasados por la configuración */
	public static $feeds = array();

	/** El contenido de los feeds extraído */
	public static $content = array();

	/** Los artículos por separado de los feeds */
	public static $items = array();

	/** El archivo a la plantilla de un feed */
	public static $feedTemplate;

	/** Lo mismo pero para lo de los items */
	public static $itemTemplate;

	/**
	 * Iniciar la configuración y coger los feeds
	 * @param array $feeds la lista url => nombre de los feeds que quieres coger
	 * @param int $posts_per_feed el número de posts de cada feed que deberían cogerse
	 * @return void
	 */
	public static function start($feeds, $posts_per_feed = 5) {
		@set_time_limit(0);
		self::$feeds = $feeds;
		foreach ($feeds as $url => $nombre) {
			self::$content[$url] = array(
				'name' => $nombre,
				'url' => $url,
				'latest_articles' => array(),
			);

			// Cogemos los 5 últimos artículos
			$latest = Google_Feeds::get($url, $posts_per_feed)->responseData->feed;
			self::$content[$url]['info'] = $latest;

			$siteUrl = $latest->link;

			// Añadir la url
			$latest->entries = array_map(function($entry) use ($siteUrl) {
				$entry->siteUrl = $siteUrl;
				return $entry;
			}, $latest->entries);

			self::$items = array_merge(self::$items, $latest->entries);
		}
	}

	/**
	 * Configurar la plantilla del feed
	 * @param string $template ruta al archivo template-fullfeed.php
	 * @return void
	 */
	public static function setFeedTemplate($template) {
		self::$feedTemplate = $template;
	}

	/**
	 * Configurar la plantilla de cada entrada
	 * @param string $template ruta al archivo template-entry.php
	 * @return void
	 */
	public static function setItemTemplate($template) {
		self::$itemTemplate = $template;
	}


	/** 
	 * Renderizar el feed con la plantilla del feed
	 * @return void
	 */
	public static function renderFeeds() {
		foreach(self::$content as $url => $_feed) {
			$feed = $_feed['info'];
			$name = $_feed['name'];
			include self::$feedTemplate;
		}
	}

	/** 
	 * Ordenar los items obtenidos
	 * @param string $by el campo por el que ordenarlos (actualmente sólo date)
	 * @return void
	 */
	public static function orderItems($by = 'date') {
		switch ($by) {
			case 'date':
			uasort(self::$items, function($a, $b) {
				return strtotime($a->publishedDate) < strtotime($b->publishedDate);
			});				
			break;
		}
	}

	/** 
	 * Renderizar cada entrada
	 * @return void
	 */
	public static function renderItems($num = -1) {
		$i = 0;
		foreach (self::$items as $entry) {
			if( $i === $num ) {
				return;
			}
			include self::$itemTemplate;
			$i++;
		}
	}
}