<?php

return array(
	/** Lista de los feeds */
	'feeds' => array(
		'http://emiliocobos.net/feed/' => 'Emilio Cobos',
		'http://ksesocss.blogspot.com/feeds/posts/default' => 'Kseso',
		'http://www.oloblogger.com/feeds/posts/default' => 'Oloblogger',
		'http://css-tricks.com/feed/' => 'CSS-Tricks',
		'http://feeds.feedburner.com/html5rocks' => 'HTML5 Rocks',
		'http://vagabundia.blogspot.com/feeds/posts/default' => 'Vagabundia',
		'http://tutorialzine.com/feed/' => 'TutorialZine',
		'http://webdesignerwall.com/feed' => 'Web Designer Wall',
		'http://paulirish.com/feed' => 'Paul Irish',
		'http://www.agamezcm.com/feeds/posts/default' => 'Antonio Gámez',
		'http://www.iniciablog.com/feeds/posts/default' => 'IniciaBlog',
		// 'http://www.compartidisimo.com/feeds/posts/default' => 'Compartidísimo',
		// ...
	),

	/** Registrar los clicks en el archivo storage/log.txt */
	'track_clicks' => true,

	/** Posts a mostrar por cada feed */
	'posts_per_feed' => 3,

	/** Posts máximos a mostrar, -1 para mostrar todos los disponibles */
	'max_posts_shown' => 30,
	
	/** No implementadas (usaremos el resumen automático de google) */
	// 'excerpt_length' => 50,
	// 'show_bloginfo' => true,
);