<?php
class Admin_Controller {
	public static $filters = array(
		'flushcache' => array('delete_cache'),
		'site' => array('change_siteconfig'),
		'themes' => array('change_theme'),
		'preview_theme' => array('change_theme'),

		// Multiple, depending on query, so not specified
		// 'manage' => array(),
		// 'edit_comment' => array('edit_comments'),
		// 'post' => array('create_posts'),

		// 'manage_comments' => array('edit_comments'),
		// 'comment' => array('edit_comments'),

		'media' => array('create_posts'),
		
		'add_user' => array('create_publisher'),
		'delete_user' => array('delete_publisher'),
		'new' => array('create_posts'),
	);
	public static function all() {
		if( ! function_exists('blogpress_version') )
			include Config::get('path.includes') . 'core-functions.php';

		// Sobreescribir configuración a lo bestia
		// Probablemente no la mejor manera, pero la única de mantener los temas sin instalaciones múltiples
		if( App::action() !== 'preview_theme' ) {
			Config::$config['path']['views'] = BASE_PATH . 'admin/views/';
			Config::$config['path']['assets'] = BASE_PATH . 'admin/assets/';
			Config::$config['path']['assets_orig'] = 'admin/assets';
		}


		if( App::action() === 'login' ) {
			if( Auth::logged_in() ) {
				return Redirect::to_route('admin');
			}
			return;
		}

		if( Auth::guest() ) {
			return Redirect::to_route('admin@login', null, 'redirect-to=' . urlencode(Url::current()));
		}

		// Asset::enqueue_style('css/bootstrap.min.css', 'head');
		Asset::enqueue_style('css/admin.css', 'head');

		Event::on('footer_start', function() {
			echo '<script>window.AppData = ' . json_encode(array(
					'user' => Auth::user(),
					'baseAssetPath' => Url::asset(''),
					'adminUrl' => Url::get('admin'),
					'roles' => Auth::roles(),
				)) . '</script>';
		});

		Asset::enqueue_script('vendor/modernizr/modernizr.js', 'head');
		Asset::enqueue_script('vendor/ecjs/js/ec.js', 'footer');
		if( file_exists(Config::get('path.assets') . 'js/' . App::action() . '.js') ) {
			Asset::enqueue_script('js/' . App::action() . '.js', 'footer');
		}

		if( isset(self::$filters[App::action()]) ) {
			foreach (self::$filters[App::action()] as $required_permission) {
				if( ! Auth::userCan($required_permission) ) {
					return self::notAllowedResponse();
				}
			}
		}
	}
	public static function notAllowedResponse() {
		App::$action = 'not-allowed';
		View::make('dashboard.not-allowed')->render(true);
		exit;
	}

	public static function action_profile() {
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			return self::update_profile();
		}
		return View::make('dashboard.profile');
	}

	public static function update_profile() {
		foreach (array(
			'username',
			'email',
			'name',
			'description',
			'url',
			'twitter_user',
			'facebook_user',
			'gplus_id'
		) as $key) {
			if( ! isset($_POST[$key]) ) {
				return View::make('dashboard.profile')
					->add_var('error', 'Hay campos que no has enviado');
			}
		}

		if( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
			echo 'El e-mail no es válido';
			return View::make('dashboard.profile')
					->add_var('error', 'El e-mail debe de ser correcto');
		}

		if( ! empty($_POST['url']) && ! filter_var($_POST['url'], FILTER_VALIDATE_URL) ) {
			return View::make('dashboard.profile')
					->add_var('error', 'La url personal debe de ser correcto');
		}

		User::find(Auth::user()->id)->set($_POST);

		Event::trigger('author.update');

		return View::make('dashboard.profile')
					->add_var('success', 'Tu perfil se ha actualizado correctamente');
	}

	/** Show the dashboard */
	public static function action_index() {
		return View::make('dashboard.index')
			->add_var('title', 'Administración');
	}

	/** Manage posts */
	public static function action_manage() {
		$per_page = 10;
		$query = Post::query();

		if( ! $order_by = Param::get('order_by') ) {
			$order_by = 'published_at';
		}
		$query->order_by($order_by);

		if( ! $status = Param::get('status') ) {
			$status = 'all';
		} else {
			$query->and_where('status', '=', $status);
		}

		if( ! $type = Param::get('type') ) {
			$type = 'all';
		} else {
			$query->and_where('type', '=', $type);
		}

		if( $author_username = Param::get('author') ) {
			if( $author = Author::where('username', '=', $author_username)->first()) {
				$query->and_where('author_id', '=', $author->id);
			} else {
				// No results
				$query->and_where('1', '!=', '1');
			}
		}

		if( $q = Param::get('q') ) {
			$query
				->and_where('title', 'LIKE', '%' . $q . '%');
				// ->or_where('content', 'LIKE', '%' . $q . '%');
		}

		if( ! ( $page = Param::get('page') ) || ! is_numeric($page)) {
			$page = 1;
		} else {
			$page = (int) $page;
		}
		$total_posts = $query->count();


		$query->limit($page * $per_page - $per_page, $per_page);
		$posts = $query->get();

		set_posts_meta($posts, array('author', 'category'));
		return View::make('dashboard.manage')
			->add_var('title', 'Administrar los posts')
			->add_var('posts', $posts)
			->add_var('page', $page)
			->add_var('status', $status)
			->add_var('type', $type)
			->add_var('total_posts', $total_posts)
			->add_var('per_page', $per_page);
	}

	public static function action_permissions() {
		$writable_files = array(
			'config.php',
			'uploads',
			'storage',
			'storage/cache',
		);

		return View::make('dashboard.permissions')
			->add_var('files', $writable_files);
	}


	/** Manage comments */
	public static function action_manage_comments() {
		$per_page = 10;
		$query = Comment::query();

		if( ! $order_by = Param::get('order_by') ) {
			$order_by = 'created_at';
		}
		$query->order_by($order_by);

		$approved = Param::get('approved');

		if(  $approved === null || ! is_numeric($approved) ) {
			$approved = 'all';
		} else {
			$query->and_where('approved', '=', (int) $approved);
		}

		if( ! $type = Param::get('type') ) {
			$type = 'all';
		} else {
			$query->and_where('type', '=', $type);
		}

		if( $author_username = Param::get('author') ) {
			if( $author = Author::where('username', '=', $author_username)->first()) {
				$query->and_where('author_id', '=', $author->id);
			} else {
				// No results
				$query->and_where('1', '!=', '1');
			}
		}

		if( ! ( $page = Param::get('page') ) || ! is_numeric($page)) {
			$page = 1;
		} else {
			$page = (int) $page;
		}
		$total_comments = $query->count();


		$query->limit($page * $per_page - $per_page, $per_page);
		$comments = $query->get();

		return View::make('dashboard.manage_comments')
			->add_var('title', 'Administrar los comentarios')
			->add_var('comments', $comments)
			->add_var('page', $page)
			->add_var('approved', $approved)
			->add_var('total_comments', $total_comments)
			->add_var('per_page', $per_page);
	}

	public static function action_edit_comment($comment_id) {
		$comment = Comment::get($comment_id);
		if( ! $comment ) {
			return Response::error(404);
		}

		if( Auth::userCan('edit_comments') || (Auth::userCan('edit_own_comments') && $comment->author_id === Auth::user()->id)) {
			return View::make('dashboard.comment')
				->add_var('comment', $comment);			
		} else {
			return self::notAllowedResponse();
		}
	}

	/** Flush the cache */ 
	public static function action_flushcache($data = null) {
		// delete all cache
		if( $data === null ) {
			Cache::flush();
		}
		// Post_id
		else if( is_numeric($data) ) {
			Cache::delete('post_' . $data . '_tags');			
			Cache::delete('post_' . $data . '_content_filtered');
		} else if ( $data === 'sitemap' ) {
			Cache::delete('sitemap');
		} else if( $data === 'all_authors' ) {
			Cache::delete('all_authors');
		} else if( $data === 'rss_feed' ) {
			Cache::delete('rss_feed');
		}
		return Redirect::to_route('admin');
	}

	/** Edit & create comments */
	public static function action_comment() {
		if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return Response::error(404);
		}
		$data = $_POST;

		/** Allow bulk actions */
		$bulk_actions = array('delete', 'approve', 'moderate');
		foreach ($bulk_actions as $action) {
			if( is_array($post_action = Param::post($action)) ) {
				$count = count($post_action);
				if( 
					Auth::userCan('edit_comments') || 
					(
						$count === 1 && 
						Auth::userCan('edit_own_comments') && 
						( $comment = Comment::get($post_action[0]) ) &&
						$comment->author_id === Auth::user()->id
					)
				) {
					$comments = Comment::where('id', 'IN', $post_action);
					switch ($action) {
						case 'delete':
							$comments->delete();
							break;
						case 'approve': 
							$comments->set(array('approved' => '1'));
							break;
						case 'moderate': 
							$comments->set(array('approved' => '0'));
							break;
						default:
							return Response::error(404);
					}
					Event::trigger('comment.' . $action, $post_action);
					return Redirect::to_route('admin@manage_comments', null, array(
						'count' =>  $count,
						'action' => $action
					));	
				} else {
					return static::notAllowedResponse();
				}
			}
		}


		if( ! Param::post('id') || ! ($comment = Comment::get(Param::post('id'))) ) {
			return Response::error(404);
		}

		if( Auth::userCan('edit_comments') || ($comment->author_id === Auth::user()->id && Auth::userCan('edit_own_comments')) ) {
			Event::trigger('comment.edit', $comment);
			Comment::find(Param::post('id'))->set($data);
			return Redirect::to_route('admin@edit_comment', Param::post('id'), array(
				'edited' => true
			));
		} else {
			return static::notAllowedResponse();
		}
	}

	/** Media managing */
	public static function action_media($ac = null) {
		if( $ac === null ) {
			return View::make('dashboard.media-view');
		}
		switch ($ac) {
			case 'upload':
				if( isset($_FILES['media']) ) {
					if( is_array($_FILES['media']) ) {
						$count = count($_FILES['media']);
						foreach ($_FILES['media'] as $file) {
							self::upload($file);
						}
					} else {
						$count = 1;
						self::upload($_FILES['media']);
					}
					return Redirect::to_route('admin@media', null, 'uploaded=' . $count);
				}
				return View::make('dashboard.media-upload');
				break;
			default:
				return Response::error(404);
		}
	}

	/** Edit & create posts */
	public static function action_post() {
		if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return Response::error(404);
		}
		foreach (array('id','title','content','slug','format','category_id') as $required_field) {
			if( ! isset($_POST[$required_field]) || (empty($_POST[$required_field]) && (int) $_POST[$required_field] !== 0 )) {
				return View::make('dashboard.post')
					->add_var('errors', array('Rellena todos los datos necesarios | ' . $required_field));
			}
		}
		if( 
			! is_numeric(Param::post('id'))
			|| ((int) Param::post('id') !== 0 && ! ($post = Post::get(Param::post('id'))) )

			|| ! is_numeric(Param::post('category_id'))
			|| ! Category::get(Param::post('category_id'))

			|| ! in_array(Param::post('format'), array('html', 'markdown'))
			|| ! (
				in_array(Param::post('status'), array('publish', 'draft')) || (Param::post('status') === null && in_array(Param::post('action'), array('delete')))
				)
		) {
			return Response::error(400); // Bad request
		}


		$caption_str = '';

		

		if( isset($post) ) {
			$previous_status = $post->status;
		} else {
			$previous_status = null;
		}
		$args = array(
			'author_id' => isset($post) ? $post->author_id : Auth::user()->id,
			'title' => Param::post('title'),
			'description' => Param::post('description'),
			'content' => Param::post('content'),
			'category_id' => Param::post('category_id'),
			'type' => Param::post('type'),
			'slug' => Param::post('slug'),
			'status' => Param::post('status'),
			'format' => Param::post('format')
		);
		if( ! 
			(
				Auth::userCan('edit_posts') || 
				(
					Auth::userCan('edit_own_posts') && 
					Auth::user()->id === $args['author_id']
				)
			) 
		) {
			return static::notAllowedResponse();
		}
		if( ($id = (int) Param::post('id')) === 0 ) {
			$post_id = Post::create($args);
		} else {
			$post_id = $id;
			if( Param::post('action') === 'delete' ) {
				Post::find($post_id)->delete();
				return Redirect::to_route('admin@manage', null, 'deleted=1');
			}
			Post::find($post_id)->set($args);
		}

		/** Redo from scratch every tag. It's the best way to ensure that all are removed */
		Post_tag::where('post_id', '=', $post_id)->delete();

		$tags = array_map('trim', array_filter(explode(',' ,Param::post('tags'))));
		if( count($tags) ) {
			foreach ($tags as $tag_name) {
				// Tag doesn't exists
				if( ! $tag = Tag::where('name', '=', $tag_name)->first() ) {
					$tag_id = Tag::create(array(
						'name' => $tag_name,
						'slug' => slugify($tag_name)
					));
				} else {
					$tag_id = $tag->id;
				}

				Post_tag::create(array(
					'post_id' => $post_id,
					'tag_id' => $tag_id
				));
			}
			Cache::delete('post_'. $post_id . '_tags');
		}

		if( Param::post('status') === 'publish' ) {
			if( $previous_status === 'draft' ) {
				Event::trigger('post.publish', array(
					'id' => $post_id
				));
			}
			return Redirect::to(Url::get(null, Param::post('slug')));
		}
		return Redirect::to(Url::get('admin@edit', $post_id));
	}

	public static function action_new() {
		return View::make('dashboard.post')
			->add_var('title', 'Nuevo post');
	}

	public static function action_edit($id) {
		$post = Post::get($id);
		if( ! $post ) {
			return Response::error(404);
		}
		if( ! 
			(
				Auth::userCan('edit_posts') || 
				(
					Auth::userCan('edit_own_posts') && 
					Auth::user()->id === $post->author_id
				)
			) 
		) {
			return static::notAllowedResponse();
		}
		set_post_meta($post); set_current_post($post);
		return View::make('dashboard.post')->add_var('post', $post);
	}

	/** Site configuration */
	public static function action_site() {
		$view = View::make('dashboard.site')
			->add_var('title', 'Opciones del sitio');
		if( $_SERVER['REQUEST_METHOD'] === 'POST') {
			foreach (array(
				'title',
				'description',
				'language',
				'webmaster_email',
				'webmaster_name',
			) as $key) {
				if( Param::post('site_' . $key) ) {
					Config::setPermanently('site.' . $key, Param::post('site_' . $key));
				}
			}
			if( Param::post('feeds_fullpost') ) {
				Config::setPermanently('feeds.fullpost', Param::post('feeds_fullpost') === 'true');
			}
			if( Config::savePermanently() ) {
				$view->add_var('success_msg', 'Has guardado la configuración correctamente');				
			} else {
				$view->add_var('error', 'Ha habido un problema al guardar la configuración. Chequea los permisos de config.php (tienen que ser <code>666</code>)');
			}
		}
		return $view;
	}

	/** Edit users */
	public static function action_users() {
		return View::make('dashboard.users')
			->add_var('users', User::all());
	}

	public static function action_json_ex() {
		return Response::json(Category::get(5));
	}

	public static function action_ajax_category_create() {
		if( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! Param::post('name')) {
			var_dump($_POST);
			return Response::error(404);
		}

		$cat = array(
			'name' => Param::post('name'),
			'slug' => slugify(Param::post('name'))
		);
		$cat_id = Category::create($cat);

		$cat['id'] = $cat_id;

		return Response::json(array(
			'category' => $cat
		));
	}

	/** Add an user */
	public static function action_add_user() {
		$view = View::make('dashboard.user-add');
		if( Param::get('ajax') === 'true' ) {
			$view->without_other_files();
		}
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$response = array();
			$args = array();
			foreach (array(
				'name',
				'username',
				'email',
				'role',
				'password',
				'password_verification'
			) as $key) {
				if( ! Param::post($key) ) {
					return Response::error(400);
				}
				$args[$key] = Param::post($key);
			}

			if( ! Auth::userCan('create_' . $args['role']) ) {
				return static::notAllowedResponse();
			}

			if( ! filter_var($args['email'], FILTER_VALIDATE_EMAIL) ) {
				return $view
						->add_var('error', 'Tienes que introducir un e-mail válido');
			}

			if( $args['password'] !== $args['password_verification'] ) {
				return $view
						->add_var('error', 'Las contraseñas deben de coincidir');
			}

			$args['password'] = Hash::make($args['password']);

			unset($args['password_verification']);

			$user_id = User::create($args);

			if( ! $user_id ) {
				if( Param::get('ajax') === 'true' ) {
					$response['error'] = 'USER_EXISTS';
					return Response::json($response);
				} else {
					return $view
						->add_var('error', 'El usuario no ha podido ser creado, el nombre de usuario o el e-mail ya están en otro usuario');
				}
			}

			if( Param::get('ajax') === 'true' ) {
				$response['user'] = User::get($user_id);
				return Response::json($response);
			}
			return Redirect::to_route('admin@users', null, 'created=' . $user_id);
		}

		return $view;
	}

	public static function action_delete_user($user_id) {
		if( ! is_numeric($user_id) || ! ($user = User::get($user_id)) || ! Auth::userCan('delete_' . $user->role) || $user->role === 'superadmin') {
			return self::notAllowedResponse();
		}

		if( $user->id !== Auth::user()->id ) {
			User::find($user_id)->delete();
		}
		return Redirect::to_route('admin@users', null, 'deleted=' . $user_id);
	}

	/** Themes config */
	public static function action_themes() {
		$view = View::make('dashboard.themes');

		if( $_SERVER['REQUEST_METHOD'] === 'POST' && ($new_theme = Param::post('theme'))) {
			
			switch (Param::post('action')) {
				case 'enable':
					Config::setPermanently('theme', $new_theme);
					if( Config::savePermanently() ) {
						$view->add_var('success_msg', 'El tema se actualizó correctamente'); 
					} else {
						$view->add_var('error_msg', 'El tema no pudo ser actualizado, comprueba los permisos de la configuración');
					}
					break;
				
				case 'preview':
					return Redirect::to_route('admin@preview_theme', $new_theme);
				default:
					return Response::error(400);
			}
		}

		$themes_dir = BASE_PATH . Config::get('path.views_orig');
		$themes = array();
		$dh = opendir($themes_dir);
		while (($theme_alias = readdir($dh)) !== false) {
			if(substr($theme_alias, 0, 1) === '.') {
				continue;
			}

			$dir = $themes_dir . DS . $theme_alias;

			if( ! is_dir($dir) ) {
				continue;
			}
			$theme_info = array();
			if( file_exists($dir . '/theme.json') ) {
				$theme_info = json_decode(file_get_contents($dir . '/theme.json'));
				if( $theme_info === null ) {
					$theme_info = array(
						'warnings' => 'El archivo theme.json está mal formado'
					);
				} else {
					$theme_info = get_object_vars($theme_info);
				}
			}
			$theme_info = (object) array_merge(array(
				'name' => 'Nombre desconocido',
				'version' => '0.0',
				'description' => 'No hay descripción disponible',
				'license' => 'Desconocida',
				'screenshots' => array(),
				'author' => (object) array(
					'name' => 'Anónimo',
					'url' => '',
					'email' => ''
				),
			), $theme_info);
			$themes[$theme_alias] = $theme_info;
		}

		$view
			->add_var('themes', $themes)
			->add_var('themes_dir', $themes_dir)
			->add_var('current_theme', Config::get('theme'));
		return $view;
	}

	public static function action_categories() {
		return View::make('dashboard.categories')
			->add_var('categories', Category::all());
	}

	public static function action_tags() {
		$view = View::make('dashboard.tags')
			->add_var('tags', Tag::all());
		if( $_SERVER['REQUEST_METHOD'] === 'POST') {
			if( Param::post('action') === 'autoclean' ) {
				$stmt = DB::$db->prepare('DELETE FROM `tags` WHERE `id` NOT IN (SELECT `tag_id` FROM `post_tags`)');
				$stmt->execute();
				$rows = (int) $stmt->rowCount();
				$view->add_var('success_msg', 'Eliminadas ' . $rows . ' etiquetas sin utilizar');
			} elseif( is_numeric(Param::post('id')) ) {
				Tag::find(Param::post('id'))->set(array(
					'name' => Param::post('name'),
					'description' => Param::post('description'),
					'slug' => Param::post('slug'),
				));
				$view->add_var('success_msg', 'La etiqueta "' . Tag::get(Param::post('id'))->name . '" ha sido actualizada correctamente');
			}
		}
		return $view;
	}


	/** Theme preview */
	public static function action_preview_theme($theme) {
		if( ! Themes::exists($theme) ) {
			return Response::error(404);
		}
		Config::set('theme', $theme);
		include Config::get('path.controllers') . 'home.php';
		Home_Controller::all();
		return Home_Controller::action_index();
	}

	/** Login-out */
	public static function login() {
		$errors = array();
		$username = Param::post('username');
		$password = Param::post('password');

		if( empty($username) || empty($password) ) {
			$errors[] = 'Introduce los datos correctamente';
		}

		if( ! $user = User::where('username', '=', $username)->first() ) {
			$errors[] = 'El usuario introducido no se encuentra en la base de datos';
		} elseif (! Hash::check($password, $user->password) ) {
			$errors[] = sprintf('La contraseña para el usuario <strong>%s</strong> no es correcta', $username);
		}

		if( count($errors) ) {
			return View::make('login.login')
				->without_other_files()
				->add_var('errors', $errors);
		}

		$_SESSION['admin_user_id'] = $user->id;

		// if( Param::post('remember') === 'on' ) {
		// 	Cookie::set('ec_login');
		// }
		if( Param::request('redirect-to') ) {
			return Redirect::to(Param::request('redirect-to'));
		}

		return Redirect::to_route('admin');
	}


	public static function action_login() {
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			return self::login();
		}
		return View::make('login.login')
			->without_other_files()
			->add_var('title', 'Iniciar sesión');
	}

	public static function action_logout() {
		$_SESSION = array();
		session_destroy();
		Redirect::to_route('admin@login');
	}
}
