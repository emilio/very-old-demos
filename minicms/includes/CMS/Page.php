<?php namespace CMS;
class Page {
	/** Tipo de página */
	public $type = '404';
	/** Subtipo, usado para páginas index (home/category) */
	public $subtype;

	/** Página actual */
	public $paged = 0;

	/** Posts por página */
	public $posts_per_page = 5;


	/** Nombre de la categoría (sólo si type = index y subtype = category) */
	public $category_name;

	/** Metadata que setearemos dependiendo del type */
	public $title;
	public $description;

	/** Constructor (vacío) */
	public function __construct() {}

	public function setMeta() {
		global $site, $post, $database, $posts;
		switch ($this->type) {
			case 'index':
				switch ($this->subtype) {
					case 'home':
						$this->url = Url::to_page($this->paged);
						$this->title = isset($site->homePageTitle) ? $site->homePageTitle : $site->name;
						$this->description = $site->description;
						break;
					case 'category':
						$this->url = Url::to_category($this->category_name, $this->paged);
						$this->title = $this->category_name . ' | ' . $site->name;
						$this->description = 'Artículos en la categoría ' . $this->category_name;
						break;
				}
				if( $this->paged > 0 ) {
					$this->title .= ' | Página ' . $this->paged;
				}
				$GLOBALS['posts'] = $database->get_posts(($this->paged - 1) * $this->posts_per_page, $this->posts_per_page, $this->subtype === 'category' ? $this->category_name : null);
				if( empty($GLOBALS['posts']) ) {
					\http_response_code(404);
					$this->subtype = null;
					$this->type = 404;
					$this->title = 'Error 404';
				}
				break;
			case 'post':
				$this->url = Url::to_post($post->slug);
				$this->title = $post->title . ' | ' . $site->name;
				if( isset($post->description) && ! empty($post->description) ) {
					$this->description = $post->description;
				}
				break;
			case '404':
				\http_response_code(404);
				$this->title = 'Error 404';
				break;
			default:
				\http_response_code(500);
				$this->type = '500';
				$this->title = 'Error 500';
				break;
		}
	}

	/** Renderizar la página */
	public function render() {
		global $post, $site, $page, $database, $posts;
		include TEMPLATE_PATH . '/header.php';
		switch ($this->type) {
			case 'post':
				include TEMPLATE_PATH . '/post.php';
				break;
			case 'index':
				// implementar
				switch ($this->subtype) {
					case 'home':
						include TEMPLATE_PATH . '/index.php';
						break;
					case 'category':
						if( file_exists(TEMPLATE_PATH . '/category.php') ) {
							include TEMPLATE_PATH . '/category.php';
						} else {
							include TEMPLATE_PATH . '/index.php';
						}
						break;
				}
				break;
			
			case '404':
				include TEMPLATE_PATH . '/404.php';
				break;
			case '500':
				include TEMPLATE_PATH . '/500.php';
				break;
		}
		include TEMPLATE_PATH . '/footer.php';
	}
}