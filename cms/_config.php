<?php
	return array(
		/*
		 * Configuración requerida
		 */
		'database' => array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'root',
			'dbname' => 'blog'
		),

		'key' => md5('DesarrollandolaCreadoPorEmilioCobos$$'),

		/*
		 * Usar la urls bonitas viene activado por defecto
		 * Rewrite es para que la aplicación genere las urls sin el index.php/
		 * En caso de que lo uses, deberás usar en el .htaccess algo así:
			<IfModule mod_rewrite.c>
			     RewriteEngine on

			     RewriteCond %{REQUEST_FILENAME} !-f
			     RewriteCond %{REQUEST_FILENAME} !-d

			     RewriteRule ^(.*)$ index.php/$1 [L]
			</IfModule>
		 */
		'url' => array(
			'pretty' => true,
			'rewrite' => true
		),

		'path' => array(
			'includes' => 'includes',
			'models' => 'models',
			'controllers' => 'controllers',
			'views' => 'themes/default',
			'assets' => 'assets',
			'cache' => 'storage/cache',
			'logs' => 'storage/logs',
		),

		'cache' => array(
			'expires' => 3, // Expiración en días de los items de la caché
		),

		// Las clases que estarán disponibles globalmente sin tener que usar el namespace
		'aliases' => array(
			'DB'       => 'EC\\Database\\DB',
			'DBObject' => 'EC\\Database\\DBObject',
			'Query'    => 'EC\\Database\\Query',
			
			'Url'      => 'EC\\HTTP\\Url',
			'Redirect' => 'EC\\HTTP\\Redirect',
			'Response' => 'EC\\HTTP\\Response',
			'Header'   => 'EC\\HTTP\\Header',
			'Param'    => 'EC\\HTTP\\Param',


			'Log'    => 'EC\\Storage\\Log',
			'Cache'  => 'EC\\Storage\\Cache',
			'Cookie' => 'EC\\Storage\\Cookie',

			'App'        => 'EC\\App',
			'Config'     => 'EC\\Config',
			'Asset'      => 'EC\\Asset',
			'Event'      => 'EC\\Event',
			'File'       => 'EC\\File',
			'Image'      => 'EC\\Image',
			'View'       => 'EC\\View',
			'Autoloader' => 'EC\\Autoloader',
			
			'Hash'       => 'EC\\Auth\\Hash',
		),

		/*
		 * Configuración extra aquí
		 */
		'homepage_posts' => 5,
		'site' => array(
			'title' => 'Pon tu sitio aquí',
			'description' => 'Descripción del sitio aquí',
			'language' => 'es-ES',
			'webmaster_email' => 'ecoal95@gmail.com',
			'webmaster_name' => 'Emilio Cobos'
		),
		'feeds' => array(
			// Mostrar el post completo en los feeds
			'fullpost' => true
		),
	);