<?php namespace EC;
use ReflectionMethod,
	EC\HTTP\Url,
	EC\HTTP\Response,
	EC\HTTP\Param,
	EC\HTTP\Redirect;


class App {
	public static $version = '0.1.0';
	public static $controller;
	public static $action;
	public static $args;
	public static $class;
	public static $url;

	public static function devCheck() {
		if( defined('DEVELOPEMENT_MODE') && DEVELOPEMENT_MODE ) {
			error_reporting(E_ALL);
			ini_set('display_errors', 'On');
		}
	}

	public static function start() {
		static::devCheck();
		static::parse_url();
	}

	public static function version() {
		return static::$version;
	}

	public static function parse_url() {
		$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if( $path === BASE_ABSOLUTE_URL ) {
			$path = '/';
		} else {
			$path = substr($path, strlen(BASE_ABSOLUTE_URL));
		}


		$path_array = array_filter(explode('/', $path));
		
		$controller = array_shift($path_array);

		$action = array_shift($path_array);

		$args = $path_array;

		// Forzar las urls para una barra, sólo en GET (para evitar problemas con POST)
		if( $path[strlen($path)-1] !== '/' && $_SERVER['REQUEST_METHOD'] === 'GET') {
			Redirect::to(Url::get($controller . '@' . $action, $args, isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null));
		}

		if( ! $controller ) {
			$controller = 'home';
		}

		if( ! $action ) {
			$action = 'index';
		}

		if( ! $args ) {
			$args = array();
		}

		$controller_path = Config::get('path.controllers');
		if( file_exists($controller_path . $controller . '.php') ) {
			require $controller_path . $controller . '.php';
			$class = ucfirst($controller) . '_Controller';
		// Si el controlador no existe, comprobamos para ver si es el home, con una acción que ahora está en $controller
		} else {
			require  $controller_path . 'home.php';
			$class = 'Home_Controller';
			if( method_exists('\\' . $class, 'action_' . $controller) ) {

				if( $action !== 'index') {
					array_unshift($args, $action);
				}
				$action = $controller;
				$controller = 'home';
			} else {
				if( $action !== 'index' ) {
					$args = array($controller, $action);
				} else {
					$args = array($controller);
				}
				$controller = 'home';
				$action = 'index';
			}
		}

		// Set the data 
		static::$controller = $controller;
		static::$action = $action;
		static::$args = $args;
		static::$class = $class;
		static::$url = Url::get($controller . '@' . $action, $args);
	}

	public static function render() {
		if( ! method_exists( '\\' . static::$class, 'action_' . static::$action) ) {
			return Event::trigger('error.404');
		}

		$reflection = new ReflectionMethod( '\\' . static::$class, 'action_' . static::$action);
		$number_of_arguments = count(static::$args);

		// Si hay más argumentos de los esperados o menos de los requeridos, lanzamos un error 404
		if( $number_of_arguments > $reflection->getNumberOfParameters() || $number_of_arguments < $reflection->getNumberOfRequiredParameters()) {
			return Response::error(404);
		}

		// Opcional una función global
		if( method_exists(static::$class, 'all') ) {
			call_user_func(array(static::$class, 'all'));
		}

		$return = call_user_func_array(array(static::$class, 'action_' . static::$action), static::$args);

		if( $return instanceof View || $return instanceof HTTP\Response ) {
			return $return->render(true);
		} else {
			echo $return;
		}
	}

	public static function url() {
		return static::$url;
	}

	public static function action() {
		return static::$action;
	}

	public static function controller() {
		return static::$controller;
	}

	public static function args() {
		return static::$args;
	}

	public static function current_class() {
		return static::$class;
	}
}
