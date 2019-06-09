<?php
	define('TOKEN', '$!FDEWJEFWIOR');// Token de seguridad, pon lo que quieras aquí, e indícaselo al panel de administración

	define('DS', DIRECTORY_SEPARATOR);
	define('PATH', dirname(__FILE__) . DS);
	define('POSTS_PATH', PATH . 'posts' . DS);

	if( '/' === DS ) { // Linux
		define('URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', PATH));
	} else { // Win
		define('URL', str_replace(DS, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DS, '/', PATH))));
	}

	if( @$_GET['token'] !== TOKEN ) {
		die('Ni se te ocurra volver a intentarlo sin el token -.-');
	}

	include 'includes/Autoloader.php';
	Autoloader::add_namespace('CMS', PATH . 'includes/CMS/');
	Autoloader::register();

	function sort_by_date($posts) {
		usort($posts, function($post_1, $post_2) {
			return -(strtotime($post_1->date) - strtotime($post_2->date));
		});
		return $posts;
	}

	$posts = array();
	$data = array(
		'posts' => array(),
		'categories' => array(),
	);

	foreach(glob(POSTS_PATH . '*.json', GLOB_NOSORT) as $filename) {
		$slug = basename($filename, '.json');
		$post = new CMS\Post($slug);
		$posts[] = $post;
	}

	$posts = sort_by_date($posts);

	foreach ($posts as $post) {
		if( $post->category === "" ) {
			continue;
		}
		if( ! isset($data['categories'][$post->category]) ) {
			$data['categories'][$post->category] = array();
		}

		$data['categories'][$post->category][] = $post->slug;
	}

	$data['posts'] = array_map(function($post) {return $post->slug;}, $posts);


	header('Content-Type: application/json');
	die(json_encode(array(
		'ok' => !! file_put_contents('data.json', json_encode($data))
	)));



