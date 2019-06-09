<?php

	/**
	 * LISTA DE VARIABLES GLOBALES
	 * $site       -> CMS\Site             -> Información del sitio
	 * $page       -> CMS\Page             -> Información de la página actual
	 * $database   -> CMS\Database         -> Información sobre posts
	 * $router     -> CMS\Router           -> Clase para manejar las urls
	 * $post       -> CMS\Post             -> Sólo si la página es de un post concreto
	 * $posts      -> array(CMS\Post, ...) -> Si la página es de tipo 'index'
	 **/

	// Tiempo de inicio de la solicitud
	define('TIME_START', isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true));
	define('DS', DIRECTORY_SEPARATOR);
	define('PATH', dirname(__FILE__) . DS);

	if( '/' === DS ) { // Linux
		define('URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', PATH));
	} else { // Win
		define('URL', str_replace(DS, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DS, '/', PATH))));
	}

	include 'includes/Autoloader.php';
	Autoloader::add_namespace('CMS', PATH . 'includes/CMS/');
	Autoloader::register();

	// Añadir un alias para las clases que queramos meter en el namespace global (por ahora solo CMS\Url)
	class_alias('CMS\Url', 'Url');

	$site = new CMS\Site();

	$site->url = URL;
	define('TEMPLATE_URL', URL . 'template/' . $site->template . '/');
	define('TEMPLATE_PATH', PATH . 'template/' . $site->template);

	define('POSTS_PATH', PATH . 'posts/');

	$page = new CMS\Page();
	$database = new CMS\Database();

	/**
	 * Aquí empieza la gracia: Tenemos 3 posibles valores para $_GET['type']:
	 * 1 - post -> renderizar un post en particular. El post vendrá en $_GET['post'];
	 * 2 - category -> renderizar los posts de una determinada categoría
	 * 3 - null -> renderizar el home
	 */
	$router = new CMS\Router();

	$router->add('(?:/page/([0-9]+))?/?', function($paged = 0) {
		global $page;
		$page->type = 'index';
		$page->subtype = 'home';
		$page->paged = (int) $paged;
	});

	$router->add('/category/([^/]+)(?:/page/([0-9]+))?/?', function($category, $paged = 0) {
		global $page;

		// por implementar: if( Category_exists )
		$page->type = 'index';
		$page->subtype = 'category';
		$page->category_name = $category;
		$page->paged = (int) $paged;
	});

	$router->add('/([^/]*)/?', function($slug) {
		global $page;
		if( file_exists(POSTS_PATH . $slug . '.json') ) {
			$page->type = 'post';
			$GLOBALS['post'] = new CMS\Post($slug);
		} else {
			$page->type = '404';
		}
	});

	$router->add('.*', function() {
		global $page;
		$page->type = '404';
	});

	$router->route();

	$page->setMeta();
	$page->render();