<?php namespace CMS;
class Router {
	/** Relative url */
	private $path;
	/** Routes added */
	public $routes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
 		$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if( $path === URL ) {
			$path = '/';
		} else {
			$path = '/' . substr($path, strlen(URL));
		}
		$this->path = $path;
	}

	/**
	 * Add a route
	 */
	public function add($expr, $callback) {
		$this->routes[] = new Route($expr, $callback);
	}

	/**
	 * Test all routes untill anyone matches
	 */
	public function route() {
		foreach ($this->routes as $route) {
			if( $route->matches($this->path) ) {
				return $route->exec();
			}
		}

		throw new \Exception("No routes matching {$path}");
		
	}
}