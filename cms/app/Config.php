<?php namespace EC;
use EC\Database\DB, EC\Storage\Cache;
class Config {
	public static $config;
	public static $original_config;
	/**
	 * Init: Edit some paths and all that stuff
	 * @return void
	 */
	public static function init() {
		if( ! static::$config ) {
			static::$config = static::$original_config = (require BASE_PATH . 'config.php');
		}

		// Hallamos las rutas absolutas
		foreach (array( 'cache','includes', 'models', 'controllers', 'views', 'assets')  as $path) {
			static::$config['path'][$path . '_orig'] = static::$config['path'][$path];
			static::$config['path'][$path] = BASE_PATH . static::$config['path'][$path] . '/';
		}

		// Configuramos la caché
		Cache::configure(array(
			'cache_path' => static::get('path.cache'),
			'expires' => static::get('cache.expires')
		));
	}

	/**
	 * Get an item from configuration
	 * @param string $key
	 * @return mixed the config value
	 */
	public static function get($key) {
		if( false !== strpos($key, '.') ) {
			list( $first, $second ) = explode('.', $key);
			return static::$config[$first][$second];
		}

		return static::$config[$key];
	}

	/**
	 * Set an item
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public static function set($key, $val) {
		if( false !== strpos($key, '.') ) {
			list( $first, $second ) = explode('.', $key);
			if( ! isset(static::$config[$first]) ) {
				static::$config[$first] = array();
			}
			static::$config[$first][$second] = $val;
		} else {
			static::$config[$key] = $val;
		}
	}

	/**
	 * Set an item in the original copy
	 * @param string $key
	 * @param mixed $val
	 * @return void
	 */
	public static function setPermanently($key, $val) {
		Config::set($key, $val);
		if( false !== strpos($key, '.') ) {
			list( $first, $second ) = explode('.', $key);
			if( ! isset(static::$original_config[$first]) ) {
				static::$original_config[$first] = array();
			}
			static::$original_config[$first][$second] = $val;
		} else {
			static::$original_config[$key] = $val;
		}
	}

	/**
	 * Save the configuration permanently, replacing the original file
	 * @return boolean wherther the config was saved or not
	 */
	public static function savePermanently() {
		// var_dump(static::$original_config); exit();
		return @file_put_contents(BASE_PATH . 'config.php', '<?php return ' . static::_formatExport(var_export(static::$original_config, true)) . ';');
	}
	public static function _formatExport($export) {
		$export = preg_replace('/[ ]{2}/', "\t", $export);
		$export = preg_replace("/\=\>[ \n\t]+array[ ]+\(/", '=> array(', $export);
		return preg_replace("/\n/", "\n\t", $export);
	}
}