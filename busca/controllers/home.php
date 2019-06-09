<?php
class Home_Controller {
	public static function all() {
		Asset::enqueue_style('css/style.css');
		Asset::enqueue_script('js/modernizr.js', 'head');
		Asset::enqueue_script('js/main.js', 'footer');
	}
	public static function action_index($word = null) {

		if( $word === null ) {
			return View::make('home.index')->add_var(array(
				'title' => 'YQueEs.com | Buscador de significado de palabras, ordenado como enciclopedia de datos útiles en internet',
				'description' => 'YQueEs.com es un buscador de significado de palabras, ordenado en forma de enciclopedia de datos y recursos útiles en internet'
			));
		}

		if( ! file_exists(API::get_file_from_path($word)) ) {
			return Response::error(404);
		}

		$term = Search::where('path', '=', $word)->first();
		$results = API::get($word);
		// Header::contentType('text/plain');
		// return print_r($results,true);
		return View::make('home.search')->add_var(array(
			'search_results' => $results,
			'search_term' => $term->formatted_term,
			'title' => '¿Qué es ' . $term->formatted_term . '?'
		));
	}

	public static function action_search() {
		if($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return Response::error(404);
		}
		if (! $term = Param::post('q') ) {
			return Redirect::to_route(null,null,'error=empty');
		}

		/** Lo hacemos lowercase */
		$term = mb_strtolower($term, 'UTF-8');

		/** Si el primer caracter no es una letra */
		if( ! in_array(substr($term, 0, 1), range('a', 'z') ) ) {
			return Redirect::to_route(null,null,'error=invalid_start');
		}

		/** Si no existe: Lo creamos */
		if( ! file_exists(API::get_file($term)) ) {
			API::create($term);
		}
		/** Tanto si existía como si no, redirigimos a la url apropiada */
		return Redirect::to_route(null, API::transform_string($term));
	}

	public static function action_letter($letter) {
		if( strlen($letter) && in_array($letter, range('a', 'z')) ) {
			$terms = Search::where('path', 'LIKE', $letter . '%')->get();
			return View::make('home.letter')->add_var('terms', $terms);
		} else {
			return Response::error(404);
		}
	}
}