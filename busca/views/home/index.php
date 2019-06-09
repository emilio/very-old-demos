<form action="<?php echo Url::get('search'); ?>" method="post" class="main-form">
	<p>
		<label for="q" class="accesible">Introduce una palabra o término</label>
		<input type="text" required="" name="q" id="q" placeholder="Palabra o término">
		<input type="submit" value="Buscar">
	</p>
</form>

<div class="module module-last-searches">
	<h2 class="module-title">Últimas búsquedas</h2>
	<div class="module-content">
	<?php
	/** Leer el directorio de las búsquedas, y coger las 10 últimas */
	$searches = Search::query()->order_by('created_at')->limit(10)->get();
	foreach ($searches as $search) {
		echo '<a href="' . Url::get(null, $search->path) . '">' . $search->formatted_term . '</a> ';
	}
	?></div>
</div>

<div class="module module-search-by-letter">
	<h2 class="module-title accesible">Búsqueda por iniciales</h2>

	<ul class="nav nav-links nav-letters">
		<?php foreach( range('a', 'z') as $letter ): ?>
			<li><a href="<?php echo Url::get('letter', $letter); ?>"><?php echo strtoupper($letter); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>