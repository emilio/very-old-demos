<?php
class Home_Controller {
	public static function all() {
		if( ! function_exists('blogpress_version') )
			include Config::get('path.includes') . 'core-functions.php';

		$theme = Config::get('theme');

		$theme_path = Config::get('path.views') . $theme . DS;

		Config::set('path.views', $theme_path);
		Config::set('path.views_orig', $theme);
		Config::set('path.assets', $theme_path);
		Config::set('path.assets_orig', $theme);

		if( file_exists($theme_path . 'functions.php') ) {
			include $theme_path . 'functions.php';	
		}

		Event::trigger('init');
	}
	public static function action_search() {
		$q = Param::get('q');
		if( $q !== null ) {
			$results = get_posts()
				->where('status', '=', 'publish')
				->and_where('title', 'LIKE', '%' . $q . '%')
				->order_by('published_at', 'desc');
			$results = handle_pagination($results);
			if( ! $results ) {
				return Response::error(404);
			}
			return View::make('search-results')
				->add_var('results', set_posts_meta($results));
		}

		if( View::exists('search') ) {
			return View::make('search');
		}

		return Response::error(404);
	}
	public static function action_sitemap() {
		Event::trigger('beforesitemap');
		Header::contentType('text/xml');
		if( ! $sitemap = Cache::get('sitemap', true) ) {
			$sitemap = Sitemap::generate();
			Cache::put('sitemap', $sitemap, true);
		}
		return $sitemap;
	}
	public static function action_feed($format) {
		switch ($format) {
			case 'rss':
				Header::contentType('text/xml');
				if( ! $rss_feed = Cache::get('rss_feed',true) ) {
					$rss_feed = \Feeds\Rss::generate();
					Cache::put('rss_feed', $rss_feed, true);
				}
				return $rss_feed;
				break;
			default:
				return Response::error(404);
		}
	}

	/**
	 * Posts individuales, página principal y páginas
	 */
	public static function action_index($slug = null) {
		if( $slug ) {
			$post = Post::where('status', '=', 'publish')->and_where('slug', '=', $slug)->first();
			if( $post ) {
				$post = set_post_meta($post);

				if( file_exists(Config::get('path.views') . $post->type . '.php') ) {
					$view = View::make($post->type);
				} else {
					$view = View::make('post');
				}
				set_current_post($post);
				return $view->add_var('post', $post);
			} else {
				return Response::error(404);
			}
		}

		$posts = get_posts()->where('status', '=', 'publish')
			->order_by('published_at', 'desc');

		$posts = handle_pagination($posts);

		if( $posts ) {
			$posts = set_posts_meta($posts);
		} else {
			return Response::error(404);
		}
		return View::make('index')
			->add_var('posts', $posts);
	}

	public static function action_tag($slug) {
		$tag = Tag::where('slug', '=', $slug)->first();
		if( ! $tag ) {
			return Response::error(404);
		}
		$GLOBALS['page_tag'] = $tag;

		$ids = Post_tag::where('tag_id', '=', $tag->id)->get('post_id');
		foreach ($ids as &$id) {
			$id = $id->post_id;
		}
		$posts = get_posts()->where('id', 'IN', $ids)
			->and_where('status', '=', 'publish')
			->order_by('published_at', 'desc');

		$posts = handle_pagination($posts);

		if( $posts ) {
			$posts = set_posts_meta($posts);
		} else {
			return Response::error(404);
		}
		return View::make('tag')
			->add_var('tag', $tag)
			->add_var('posts', $posts);
	}

	public static function action_category($slug) {
		$category = Category::where('slug', '=', $slug)->first();
		if( ! $category ) {
			return Response::error(404);
		}
		$GLOBALS['page_category'] = $category;
		$posts = Post::where('category_id', '=', $category->id)
			->and_where('status', '=', 'publish')
			->and_where('type', '=', 'post')
			->order_by('published_at', 'desc');

		$posts = handle_pagination($posts);

		if( $posts ) {
			$posts = set_posts_meta($posts);
		} else {
			return Response::error(404);
		}
		return View::make('category')
			->add_var('category', $category)
			->add_var('posts', $posts);
	}
	public static function action_authors($username = null) {
		if( $username ) {
			if( ! $author = Author::where('username', '=', $username)->first() ) {
				return Response::error(404);
			}
			$author->posts = Post::where('author_id', '=', $author->id)
				->and_where('status', '=', 'publish')
				->and_where('type', '=', 'post')
				->order_by('published_at', 'desc')
				->limit(3)
				->get();

			// No mostrar autores sin posts
			if( Config::get('author_requires_posts') && ! count($author->posts)) {
				return Response::error(404);
			}

			// Sólo cargar las categorías, ni el autor, ni las etiquetas
			$author->posts = set_posts_meta($author->posts, array('category'));

			$GLOBALS['page_author'] = $author;

			return View::make('author')
				->add_var('author', $author)
				->add_var('posts', $author->posts);
		}

		if( ! $authors = Cache::get('all_authors') ) {
			$authors = Author::all();

			foreach ($authors as $index => $author) {
				$author->last_post = Post::where('author_id', '=', $author->id)
					->and_where('status', '=', 'publish')
					->and_where('type', '=', 'post')
					->order_by('published_at', 'desc')
					->first();
				if( $author->last_post === null ) {
					unset($authors[$index]);
				} else {
					set_post_meta($author->last_post);
				}
			}
			Cache::put('all_authors', $authors);
		}
		Header::contentType('text/plain');
		var_dump($authors);exit;
		return View::make('authors')
			->add_var('authors', $authors);
	}
}