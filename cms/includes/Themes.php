<?php
class Themes {
	public static $themes;
	public static $dir;
	public static function init() {
		static::setDir();
	}
	public static function setDir() {
		static::$dir = BASE_PATH . Config::get('path.views_orig');
	}
	public static function get() {
		if( ! isset(static::$themes) ) {
			$themes = array();
			$dh = opendir(static::$dir);
			while (($theme_alias = readdir($dh)) !== false) {
				if(substr($theme_alias, 0, 1) === '.') {
					continue;
				}

				$dir = static::getThemeDir($theme_alias);

				if( ! is_dir($dir) ) {
					continue;
				}
				$themes[$theme_alias] = static::getInfo($theme_alias);
			}
			static::$themes = $themes;
		}
		return static::$themes;
	}

	public static function getThemeDir($theme) {
		return static::$dir . DS . $theme;
	}

	public static function exists($theme) {
		return is_dir(static::getThemeDir($theme));
	}

	public static function getThemeUrl($theme) {
		return  Url::get() . Config::get('path.views_orig') . '/' . $theme . '/';
	}

	public static function getInfo($theme) {
		$dir = static::getThemeDir($theme);
		$theme_info = array();
		if( file_exists($dir . '/theme.json') ) {
			$theme_info = json_decode(file_get_contents($dir . '/theme.json'));
			if( $theme_info === null ) {
				$theme_info = array(
					'warnings' => 'El archivo theme.json está mal formado'
				);
			} else {
				$theme_info = get_object_vars($theme_info);
			}
		}
		$theme_info = (object) array_merge(array(
			'name' => 'Nombre desconocido',
			'version' => '0.0',
			'description' => 'No hay descripción disponible',
			'license' => 'Desconocida',
			'screenshots' => array(),
			'author' => (object) array(
				'name' => 'Anónimo',
				'url' => '',
				'email' => ''
			),
		), $theme_info);
		return $theme_info;
	}
}

Themes::init();