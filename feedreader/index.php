<?php 
/** Algunas constantes de utilidad */
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__FILE__) . DS);
if( '/' === DS ) {
	define('BASE_ABSOLUTE_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH));
} else {
	define('BASE_ABSOLUTE_URL', str_replace(DS, '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DS, '/', BASE_PATH))));
}


/** Autocarga de clases en plan sencillo */
spl_autoload_register(function($class) {
	require BASE_PATH . 'includes/' . $class . '.php';
});

/** Obtener la configuración */
$config = (require BASE_PATH . 'includes/config.php');

/** Queremos registrar los clicks? */
define('TRACK_CLICKS', isset($config['track_clicks']) ? $config['track_clicks'] : false);

/** incluir las funciones de formato */
require BASE_PATH . 'includes/functions.php';

/** Configurar la caché para que expire en una hora */
Cache::configure(array(
	'cache_path' => BASE_PATH . 'storage/cache',
	'expires' => 6 // 6 horas
));

/** 
 * Sólo usar la clase si no tenemos una copia completa fresca desde la caché
 */
if( ! $items = Cache::get('Reader_full_items', true) ) {
	Reader::start($config['feeds'], $config['posts_per_feed']);
	// Por ahora sólo soportado date
	Reader::orderItems('date');
	Reader::setFeedTemplate(BASE_PATH . 'includes/template-fullfeed.php');
	Reader::setItemTemplate(BASE_PATH . 'includes/template-entry.php');

	ob_start();
	Reader::renderItems();
	$items = ob_get_clean();

	Cache::put('Reader_full_items', $items, true);
	echo '<!--
		Items leídos desde el principio
		Url base: ' . BASE_ABSOLUTE_URL . '
	-->';
}
?><!DOCTYPE html>
<!--[if lt IE 7 & (!IEMobile)]>
<html class="ie ie6 lt-ie7 lt-ie8 lt-ie9 no-js">
<![endif]-->
<!--[if (IE 7) & (!IEMobile)]>
<html class="ie ie7 lt-ie8 lt-ie9 no-js">
<![endif]-->
<!--[if (IE 8) & (!IEMobile)]>
<html class="ie ie8 lt-ie9 no-js">
<![endif]-->
<!--[if IE 9 & (!IEMobile)]>
<html class="ie ie9 no-js">
<![endif]-->
<!--[if (gt IE 9) | (IEMobile) | !(IE)  ]><!-->
<html class="no-js">
<!--<![endif]-->
<head prefix="og: http://ogp.me/ns#">
	<meta charset="utf-8">

	<title>Blogs sobre diseño web, blogger y wordpress | Agregador de feeds con PHP | Emilio Cobos</title>
	<meta property="og:title" content="Blogs sobre diseño y desarrollo web, blogger y wordpress | Agregador de feeds con PHP | Emilio Cobos">

	<meta name="description" content="Red de artículos de blogs externos sobre diseño y desarrollo web que te pueden ser de ayuda.">
	<meta property="og:description" content="Red de artículos de blogs externos sobre diseño y desarrollo web que te pueden ser de ayuda.">

	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet" href="css/style.css">
	<!--[if lt IE 9]>
		<script src="js/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div class="header-outer">
		<header role="banner" class="site-header container">
			<h1 class="site-title"><a href="<?php echo BASE_ABSOLUTE_URL ?>">FeedReader | Lista de blogs interesantes sobre diseño y desarrollo web, blogger...</a></h1>
		</header>
	</div>
	<ul role="main" class="feed-items container">
		<?php /** Mostrar los elementos desde la caché */ 
			echo $items;
		?>
	</ul>
	<?php // NetK::renderFeeds() ?>
	<div class="footer-outer">
		<footer class="site-footer container">
			<p>Pequeño experimento por <a href="//emiliocobos.net/" rel="author" title="Diseño y desarrollo web">Emilio Cobos</a>. Basado en <a href="//ksesocss.blogspot.com/2012/11/netK.html">NetK</a></p>
			<p>
				<a href="//emiliocobos.net/agregador-feeds-php/#llamamiento">¿Quieres salir listado?</a>
			</p>
		</footer>
	</div>
	<script>
		var _gaq = _gaq || [];

		_gaq.push(['_setAccount', 'UA-27809661-2']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
</body>
</html>
