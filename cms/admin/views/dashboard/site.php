<div class="wrapper">
	<?php if( isset($success_msg) ): ?>
		<div class="message message-success"><?php echo $success_msg ?></div>
	<?php endif; ?>
	<?php if( isset($error) ): ?>
		<div class="message message-error"><?php echo $error ?></div>
	<?php endif; ?>
	<form action="<?php echo Url::get('admin@site') ?>" method="POST">
		<h2 class="section-title">Datos generales</h3>
		<p>
			<label for="site_title">Título del sitio</label>
			<input class="text-input" type="text" name="site_title" id="site_title" value="<?php echo Config::get('site.title') ?>">
		</p>
		<p>
			<label for="site_description">Descripción del sitio</label>
			<input class="text-input" type="text" name="site_description" id="site_description" value="<?php echo Config::get('site.description') ?>">
		</p>
		<p>
			<label for="site_language">Idioma del sitio</label>
			<?php $current = Config::get('site.language');
			$languages = Config::get('available_languages'); ?>
			<select name="site_language" id="site_language">
				<?php foreach ($languages as $key => $language): ?>
					<option value="<?php echo $key ?>"<?php if($key === $current) {echo' selected';} ?>><?php echo $language ?></option>
				<?php endforeach ?>
			</select>
		</p>
<!-- 		<p class="submit">
			<input class="btn btn-info" type="submit" value="Guardar configuración">
		</p> -->
		
		<h2 class="section-title">Configuración de los feeds</h3>
		<p>
			<?php $current = Config::get('feeds.fullpost'); ?>
			<label for="feeds_fullpost">Resúmenes</label>
			<select name="feeds_fullpost" id="feeds_fullpost">
				<option value="true"<?php if($current) echo ' selected'; ?>>Mostrar el post entero en el feed</option>
				<option value="false"<?php if(!$current) echo ' selected'; ?>>Mostrar un resumen</option>
			</select>
		</p>
		<p class="submit">
			<input class="btn btn-info" type="submit" value="Guardar configuración">
		</p>
	</form>
</div>