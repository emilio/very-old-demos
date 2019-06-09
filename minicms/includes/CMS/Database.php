<?php namespace CMS;
/* No es una base de datos real! no te asustes. Sólo leemos los datos temporales generados al publicar un post y los usamos para cargar datos acerca de categorías y demás */
class Database {
	private $data;
	public function __construct() {
		if( file_exists(PATH . 'data.json') ) {
			$this->data = json_decode(file_get_contents(PATH . 'data.json'));	
		} else {
			throw new \Exception("File data.json not found");
		}
	}
	public function __get($prop) {
		return $this->data->{$prop};
	}
	public function __set($prop, $val) {
		$this->data->{$prop} = $val;
	}
	public function __isset($prop) {
		return isset($this->data->{$prop});
	}

	/* Coge un número de posts, desde $from hasta llegar a $many posts. Opcionalmente desde una categoría. Por ejemplo:
	 * $database->get_pòsts(0, 5) retorna los 5 primeros posts
	 */
	public function get_posts($from, $many, $from_category = null) {
		if( $from_category === null ) {
			$source = $this->data->posts;
		} else {
			$source = isset($this->data->categories->{$from_category}) ? $this->data->categories->{$from_category} : array();
		}

		return array_map(function($slug) {return new Post($slug);}, array_slice($source, $from, $many));
	}
}