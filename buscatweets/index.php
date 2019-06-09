<?php
	define('BASE', dirname(__FILE__));
	// Pon aquí tus datos
	define('APP_CONSUMER_KEY', '');
	define('APP_CONSUMER_SECRET', '');
	define('ACCESS_TOKEN', '');
	define('ACCESS_TOKEN_SECRET', '');
	// activar la caché?
	define('CACHE_ENABLED', false);


	// Parsear el texto del tweet, añadiendo menciones, links y hashtags
	function tweet_text($text) {
		// urls: lo primero para que no conviertan los links posteriores
		$text =  preg_replace("/(http:\/\/[^\s]+)/", '<a href="$1" class="tweet-link" target="_blank">$1</a>', $text);
		// menciones
		$text =  preg_replace("/(^|\W)@([A-Za-z0-9_]+)/", '$1<a href="http://twitter.com/$2" class="tweet-mention" target="_blank">@$2</a>', $text);
		// hashtags
		$text =  preg_replace("/(^|\W)#([^\s]+)/", '$1<a href="?q=%23$2" class="tweet-hash" target="_blank">#$2</a>', $text);
		return $text;
	}
	// Si estamos en la página de resultados
	if( isset($_GET['q']) && !empty($_GET['q']) ) {
		$results = null;
		if( CACHE_ENABLED ) {
			// Lo primero que hacemos es configurar la caché y ver si hay resultados almacenados
			include 'inc/Cache.php';
			Cache::configure(array(
				'cache_dir' => BASE . '/cache',
				'expires' => 1 // hora
			));
			$results = Cache::get($_GET['q']);
		}
		// Si no hay resultados previamente almacenados en la caché
		if( ! $results ) {
			// Incluímos la librería, hacemos la búsqueda y lo guardamos
			include 'inc/twitteroauth.php';

			$twitteroauth = new TwitterOAuth(APP_CONSUMER_KEY, APP_CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
			$results = $twitteroauth->get('search/tweets', array(
				'q' => $_GET['q'], // Nuestra consulta
				'lang' => 'es', // Lenguaje (español)
				'count' => 5, // Número de tweets
				'include_entities' => false, // No nos interesa información adicional. Ver: https://dev.twitter.com/docs/tweet-entities
			));
			if( CACHE_ENABLED ) {
				Cache::put($_GET['q'], $results);
			}
		}


	}
?><!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<?php if(isset($results)): ?>
			<title>Resultados de tweets para: <?php echo $_GET['q'] ?></title>
		<?php else: ?>
			<title>Buscatweets | Emilio Cobos</title>
			<meta name="description" content="Búsqueda de tweets con PHP">
		<?php endif;?>

		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<div class="container">
			<form class="main-form" action="" method="GET">
				<label for="q" class="assistive-text">Introduce el término de búsqueda.</label>
				<input type="text" id="q" name="q" value="<?php echo @$_GET['q'] ?>" placeholder="Pon lo que quieras" required>
				<button type="submit"><span class="assistive-text">Buscar</span></button>
			</form>
			<?php if(isset($results)): ?>
				<div class="search-results">
					<?php if(count($results->statuses)): ?>
						<?php foreach ($results->statuses as $tweet): ?>
							<div class="tweet">
								<div class="tweet-user">
									<img class="tweet-user-image" src="<?php echo $tweet->user->profile_image_url ?>" alt="@<?php echo $tweet->user->screen_name ?>">
									<a class="tweet-user-link" href="http://twitter.com/<?php echo $tweet->user->screen_name ?>" title="@<?php echo $tweet->user->screen_name ?>"><?php echo $tweet->user->name ?></a>
								</div>
								<p class="tweet-text"><?php echo tweet_text($tweet->text); ?></p>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="error-message">No se ha encontrado ningún tweet</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</body>
</html>
