<?php
	/**
	 * This is the main functions file of BlogPress
	 * @package Bloggpress
	 * @since 1.0
	 */

	/**
	 * Set the default encoding for mb_ functions
	 */
	mb_internal_encoding('UTF-8');

	/**
	 * Returns the current version
	 */
	function blogpress_version() {
		return '0.1Beta';
	}

	/**
	 * Modify the current query for showing paginations
	 * @param Query $query
	 * @return Query
	 */
	function handle_pagination($query) {
		$posts_per_page = Config::get('homepage_posts');
		if( ! $page = Param::get('page') ) {
			$page = 1;
		}
		if( is_numeric($page) ) {
			$page = intval($page, 10);

			$first = ($page - 1) * $posts_per_page;
			$query->limit($first, $posts_per_page);

			$result = $query->get();

			if( ! count($result) ) {
				return null;				
			}
		} else {
			return Redirect::to(Url::current(), 301);
		}

		return $result;
	}

	/* =========================================
		Database functions
	========================================== */
	/**
	 * Get a query for the pages
	 * @return \EC\Database\Query the query
	 */
	function get_pages() {
		return Post::where('type', '=', 'page');
	}

	/**
	 * Get a query for the posts
	 * @return \EC\Database\Query the query
	 */
	function get_posts() {
		return Post::where('type', '=', 'post');
	}


	/* =========================================
		Post functions
	========================================== */
	/**
	 * Creates an slug from a string
	 * @param string $str the string to convert
	 * @return string the slug
	 */
	function slugify($str) {
		return str_replace(' ', '-', mb_strtolower($str));
	}

	/**
	 * Set the current global post
	 * @param stdClass $post
	 */
	function set_current_post($post) {
		$GLOBALS['post'] = $post;
	}

	/**
	 * Get the current post object
	 * @global $post
	 */
	function get_current_post() {
		global $post;
		return $post;
	}

	/**
	 * Get the previous post
	 * @return stdClass|null
	 */
	function get_previous_post() {
		static $previous_post;
		if( isset($previous_post) ) {
			return $previous_post;
		}

		global $post;
		if( ! $previous_post = Cache::get('post_' . $post->id . '_previous_post') ) {
			$previous_post = get_posts()->where('status', '=', 'publish')
				->and_where('published_at', '<', $post->published_at)
				->order_by('published_at', 'desc')
				->first();
			Cache::put('post_' . $post->id . '_previous_post', $previous_post);
		}

		return $previous_post;
	}
	/**
	 * Get the previous post
	 * @return stdClass|null
	 */
	function get_next_post() {
		static $next_post;
		if( isset($next_post) ) {
			return $next_post;
		}

		global $post;

		if( ! $next_post = Cache::get('post_' . $post->id . '_next_post') ) {
			$next_post = get_posts()->where('status', '=', 'publish')
				->and_where('published_at', '>', $post->published_at)
				->order_by('published_at', 'asc')
				->first();
			Cache::put('post_' . $post->id . '_next_post', $next_post);
		}
		return $next_post;
	}


	/**
	 * Set the metadata for a post
	 * Options include tags, category and author
	 * @see set_posts_meta
	 * @param stdClass $post
	 * @param array $include the fields to include
	 * @return stdClass the post with the metadata
	 */
	function set_post_meta($post, $include = array('tags', 'category', 'author')) {
		$posts = set_posts_meta(array($post), $include);
		return $posts[0];
	}

	/**
	 * Set post metadata to various posts
	 * Do it with an array instead of post-by-post lets us perform simple "caching"
	 * @see set_post_meta
	 * @param array $posts the list of posts you want to set the metadata
	 * @param array $include
	 * @return array
	 */
	function set_posts_meta($posts, $include = array('tags', 'category', 'author')) {
		$authors = array();
		$categories = array();
		foreach ($posts as &$post) {
			if( in_array('author', $include) && ! isset($post->author) ) {
				if( ! isset($authors[$post->author_id]) ) {
					$authors[$post->author_id] = Author::get($post->author_id);
				}
				$post->author = $authors[$post->author_id];
			}
			if( in_array('category', $include) && ! isset($post->category) ) {
				if( ! isset($categories[$post->category_id]) ) {
					$categories[$post->category_id] = Category::get($post->category_id);
				}
				$post->category = $categories[$post->category_id];
			}
			if( in_array('tags', $include) && ! isset($post->tags) ) {
				$post->tags = get_post_tags($post->id);
			}
		}
		return $posts;
	}
	
	/**
	 * Get a single post tags
	 * @param int $post_id
	 */
	function get_post_tags($post_id = null) {
		if( ! $post_id ) {
			$post_id = get_current_post()->id;
		}
		if( ! $tags = Cache::get('post_' . $post_id . '_tags') ) {
			$tags = array();
			$tags_ids = Post_tag::where('post_id', '=',$post_id)->get();

			foreach ($tags_ids as $tag) {
				$tags[] =  Tag::get($tag->tag_id);
			}

			Cache::put('post_' . $post_id . '_tags', $tags);
		}
		return $tags;
	}

	/* =========================================
		Conditional functions
	========================================== */
	function is_home() {
		return App::url() === Url::get();
	}

	function is_post() {
		return isset($GLOBALS['post']);
	}

	function is_page() {
		return isset($GLOBALS['post']) && $GLOBALS['post']->type === 'page';
	}

	function is_search() {
		return App::action() === 'search';
	}

	function is_search_results() {
		return is_search() && Param::get('q') !== null;
	}

	function is_tag() {
		return App::action() === 'tag';
	}

	function is_category() {
		return App::action() === 'category';
	}

	function is_authors() {
		return App::action() === 'authors';
	}

	function is_author_page() {
		return is_authors() && isset($GLOBALS['page_author']);
	}

	function is_error() {
		return App::controller() === 'error';
	}

	function has_next_post() {
		return is_post() && get_next_post() !== null;
	}

	function has_previous_post() {
		return is_post() && get_previous_post() !== null;
	}

	/* =========================================
		Especific page functions
	========================================== */
	function get_page_category() {
		if( is_category() ) {
			return $GLOBALS['page_category'];
		}
		return null;
	}

	function get_page_tag() {
		if( is_tag() ) {
			return $GLOBALS['page_tag'];
		}
		return null;
	}

	function get_page_author() {
		if( is_author_page() ) {
			return $GLOBALS['page_author'];
		} elseif( is_post() ) {
			return get_current_post()->author;
		}
		return null;
	}

	/**
	 * Get the previous post url
	 * @see has_previous_post();
	 * @see get_previous_post();
	 * @return string the url or empty if failed
	 */
	function get_previous_post_url() {
		if( ! has_previous_post() ) {
			return '';
		}
		return get_post_url(get_previous_post());
	}
	/**
	 * Get the next post url
	 * @see has_next_post();
	 * @see get_next_post();
	 * @return string the url or empty if failed
	 */
	function get_next_post_url() {
		if( ! has_next_post() ) {
			return '';
		}
		return get_post_url(get_next_post());
	}
	/**
	 * Echo the previous post url
	 * @see get_previous_post_url();
	 */
	function the_previous_post_url() {
		echo get_previous_post_url();
	}
	/**
	 * Echo the next post url
	 * @see get_next_post_url();
	 */
	function the_next_post_url() {
		echo get_next_post_url();
	}

	/* =========================================
		Template functions
	========================================== */
	/**
	 * Get the current page title
	 * @param string $separator
	 * @return string the title 
	 */
	function get_page_title($separator = ' | ') {
		$title = null;
		if(is_home()) {
			return Config::get('site.title') . $separator . Config::get('site.description');
		} elseif (is_post()) {
			$title = get_post_title();
		} elseif( is_search() && ! is_search_results() ) {
			$title = 'Búsqueda';
		} elseif( is_category() ) {
			$title = get_page_category()->name;
		} elseif (is_tag()) {
			$title = get_page_tag()->name;
		} elseif( is_author_page() ) {
			$title = get_page_author()->name . ' (' . get_page_author()->username . ')';
		} elseif ( is_authors() ) {
			$title = 'Autores';
		} elseif (is_search_results()) {
			$title = 'Resultados de búsqueda para "' . Param::get('q') . '"';
		} elseif( is_error() ) {
			$title = 'Error';
		} else {
			throw new Exception('Description couldn\'t be generated');
		}

		return $title . $separator . Config::get('site.title');
	}
	/**
	 * Display the current page title, html escaped
	 * @param string $separator
	 * @see get_page_title();
	 */
	function the_page_title($separator = ' | ') {
		echo htmlspecialchars(get_page_title($separator));
	}


	/**
	 * Get the current page title
	 * @param string $separator
	 * @return string the title 
	 */
	function get_page_description() {
		if(is_home()) {
			return Config::get('site.description');
		} elseif (is_post()) {
			return get_post_description();
		} elseif( is_search() ) {
			return 'Busca en ' . Config::get('site.title');
		} elseif( is_category() ) {
			return get_page_category()->description;
		} elseif (is_tag()) {
			return get_page_tag()->description;
		} elseif( is_author_page() ) {
			return get_page_author()->description;
		} elseif ( is_authors() ) {
			return 'Lista de autores en ' . Config::get('site.title');
		} elseif( is_error() || is_search_results() ) {
			return '';
		} else {
			throw new Exception('Description couldn\'t be generated');
		}
	}

	function page_head() {
		Asset::print_styles('head');
		Asset::print_scripts('head');
		Event::trigger('blog.head_end');
	}
	/**
	 * Display the current page description, html escaped
	 * @param string $separator
	 * @see get_page_description();
	 */
	function the_page_description() {
		echo htmlspecialchars(get_page_description());
	}



	/* =========================================
		Template functions: Post
	========================================== */
	/**
	 * The post content filter
	 * @param string|stdClass $post
	 * @return string the parsed string or post content
	 */
	function filter_content($post) {
		if( is_string($post) ) {
			return Format::do_shortcode($post);
		} else {
			if( ! $parsed_body = Cache::get('post_' . $post->id . '_content_filtered', true) ) {
				if( $post->format === 'markdown' ) {
					$post->content = Markdown\Parser::parse($post->content);
				}
				$parsed_body = filter_content($post->content);
				Cache::put('post_' . $post->id . '_content_filtered', $parsed_body, true);
			}
			return $parsed_body;
		}
	}

	/**
	 * Get the post url
	 * @global $post
	 * @return string the post url
	 */
	function get_post_url($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return Url::get('home@index', $post->slug);
	}

	/**
	 * Echo the post url
	 * @see get_post_url()
	 */
	function the_post_url($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_url($post);
	}

	/**
	 * Get the post title
	 * @global $post
	 * @return string the post title html escaped
	 */
	function get_post_title($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return $post->title;
	}
	/**
	 * Echo the post title
	 * @see get_post_title()
	 */
	function the_post_title($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo htmlspecialchars(get_post_title($post));
	}

	/**
	 * Get the post published date in the format especified
	 * @param string $format a valid date format
	 */
	function get_post_published_date($format = 'c', $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return date($format, strtotime($post->published_at));
	}
	/**
	 * Echo the post published date in the format especified
	 * @param string $format a valid date format
	 * @see get_post_published_date();
	 */
	function post_published_date($format = 'c', $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_published_date($format, $post);
	}

	/**
	 * Get the post published date in the format especified
	 * @param string $format a valid date format
	 */
	function get_post_updated_date($format = 'c', $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return date($format, strtotime($post->updated_date_at));
	}
	/**
	 * Echo the post published date in the format especified
	 * @param string $format a valid date format
	 * @see get_post_published();
	 */
	function post_updated_date($format = 'c', $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_updated_date($format, $post);
	}

	/**
	 * Get the post content
	 * @global $post
	 * @see filter_content();
	 * @return string the post content
	 */
	function get_post_content($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return filter_content($post);
	}
	/**
	 * Echo the post content
	 * @see get_post_content()
	 */
	function the_post_content($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_content($post);
	}

	/**
	 * Get the post description
	 * @global $post
	 * @see get_post_excerpt()
	 * @return string the post description if set, an excerpt if not
	 */
	function get_post_description($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		if( $post->description ) {
			return $post->description;
		} else {
			return get_post_excerpt(100) . '...';
		}
	}
	/**
	 * Echo the post description
	 * @see get_post_description();
	 */
	function the_post_description($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_description($post);
	}

	/**
	 * Get the post type
	 * @global $post
	 * @return string the post type
	 */
	function get_post_type($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return $post->type;
	}
	/**
	 * Echo the post title
	 * @see get_post_title()
	 */
	function the_post_type($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_type($post);
	}	

	/**
	 * Get the post excerpt of given length
	 * @param int $len the excerpt length
	 * @see get_post_description();
	 * @see get_post_content();
	 * @return string a substring of the post content
	 */
	function get_post_excerpt($len = 90, $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return trim(mb_substr(strip_tags(get_post_content($post)), 0, $len));
	}
	/**
	 * Echo the post excerpt
	 * @param int $len the excerpt length
	 * @see get_post_description();
	 */
	function the_post_excerpt($len = 90, $post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_excerpt($len, $post);
	}

	/**
	 * Get the url of the post's category
	 * @global $post
	 * @return string the post's category url
	 */
	function get_post_category_url($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		return Url::get(null, array('category', $post->category->slug));
	}
	/**
	 * Echo the url of the post's category
	 * @see get_post_category_url()
	 */
	function the_post_category_url($post = null) {
		if( $post === null ) {
			$post = $GLOBALS['post'];
		}
		echo get_post_category_url($post);
	}

	/**
	 * Get the post's category name html escaped
	 * @global $post
	 * @return string the category name
	 */
	function get_post_category_name() {
		global $post;
		return htmlspecialchars($post->category->name);
	}
	/**
	 * Echo the post's category name html escaped
	 * @see get_post_category_name()
	 */
	function the_post_category_name() {
		echo get_post_category_name();
	}

	/**
	 * Get a tag url
	 * @param stdClass $tag the tag object
	 * @return string the url
	 */
	function get_tag_url($tag) {
		return Url::get('tag', $tag->slug);
	}
	/**
	 * Echo a tag url
	 * @see get_tag_url();
	 * @param stdClass $tag the tag object
	 */
	function the_tag_url($tag) {
		echo get_tag_url($tag);
	}




	/**
	 * @todo write this
	 */
	function get_post_caption_url() {}

	/* =========================================
		Template functions: Author
	========================================== */
	/**
	 * Get the post's author
	 * @global $post
	 * @see set_post_meta
	 * @return stdClass the author object
	 */
	function get_post_author() {
		global $post;
		if( ! isset($post->author) ) {
			$post = $GLOBALS['post'] = set_post_meta($post, array('author'));
		}

		return $post->author;
	}

	/**
	 * Get any post author field
	 * @param string $field the field name
	 * @see get_post_author
	 * @return mixed the field value
	 */
	function get_post_author_meta($field) {
		return get_post_author()->{$field};
	}
	/**
	 * Echo any post author meta
	 * @param string $field the field name
	 * @see get_post_author_meta
	 */
	function the_post_author_meta($field) {
		echo get_post_author_meta($field);
	}

	/**
	 * Get the current post author url
	 * @see get_post_author_meta
	 * @return string the url
	 */
	function get_post_author_url() {
		return Url::get('authors', get_post_author_meta('username'));
	}
	/**
	 * Echo the current post author url
	 * @see get_post_author_url();
	 */
	function the_post_author_url() {
		echo get_post_author_url();
	}

	/**
	 * Get the current post author name
	 * @see get_post_author_meta
	 * @return string the name
	 */
	function get_post_author_name() {
		return get_post_author_meta('name');
	}
	/**
	 * Echo the current post author name
	 * @see get_post_author_name();
	 */
	function the_post_author_name() {
		echo get_post_author_name();
	}


	/**
	 * Get an avatar of the user of an email based on gravatar
	 * @param string $email the email
	 * @param int $size the image width
	 * @param string $default the default image
	 * @return string the image url
	 */
	function get_avatar($email, $size = 50, $default = 'mm') {
		return "//gravatar.com/avatar/" . md5(trim(strtolower($email))) . "?s=$size&amp;d=$default";
	}

	/**
	 * Get the post author avatar url
	 * @see get_avatar()
	 * @param int $size the image width
	 * @param string $default the default image
	 * @return string the image url
	 */
	function get_post_author_image_url($size = 50, $default = 'mm') {
		return get_avatar(get_post_author_meta('email'), $size, $default);
	}
	/**
	 * Echo the post author avatar url
	 * @see get_post_author_image_url();
	 * @param int $size the image width
	 * @param string $default the default image
	 */
	function the_post_author_image_url($size = 50, $default = 'mm') {
		echo get_post_author_image_url($size, $default);
	}


	/** ===============================
	Image functions
	==================================*/
	/**
	 * Upload an image
	 * @param Image $image
	 * @return mixed the image url on success, false on failure
	 */
	function upload_image(Image $image) {
		$uploads_dir = BASE_PATH . 'uploads' . DS;
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		if( ! is_dir($uploads_dir . $year ) ) {
			mkdir($uploads_dir . $year);
		}

		if( ! is_dir($uploads_dir . $year . DS . $month) ){
			mkdir($uploads_dir . $year . DS . $month);
		}

		if( ! is_dir($uploads_dir . $year . DS . $month . DS . $day) ) {
			mkdir($uploads_dir . $year . DS . $month . DS . $day);
		}

		if( $image->save($uploads_dir . $year . DS . $month . DS . $day . DS . $image->basename) ) {
			return implode('/', array($year, $month, $day, $image->basename));
		}

		return false;
	}

	/** ===============================
	Session functions
	================================== */
	function is_user_logged_in() {
		return Auth::logged_in();
	}
	function user() {
		return Auth::user();
	}


	function post_admin_links() {
		global $post;
		if( is_user_logged_in() ): ?>
		<a href="<?php echo Url::get('admin@flushcache', $post->id) ?>" class="admin-link">Borrar caché del post</a>
		<a href="<?php echo Url::get('admin@edit', $post->id) ?>" class="admin-link">Editar post</a>
		<?php endif;
	}