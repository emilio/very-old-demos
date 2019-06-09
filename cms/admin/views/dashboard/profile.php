<?php
function posted_or_from_user($key) {
	return Param::post($key) ? Param::post($key) : Auth::user()->{$key};
} ?>
<form action="<?php echo Url::get('admin@profile') ?>" class="wrapper" method="POST">
	<?php if( isset($error) ): ?>
	<div class="message message-error"><?php echo $error ?></div>
	<?php elseif (isset($success)): ?>
	<div class="message message-success"><?php echo $success ?></div>
	<?php endif; ?>
	<h2 class="section-title">Datos básicos</h2>
	<p>
		<label for="username">Nombre de usuario:</label>
		<input class="text-input" type="text" id="username" name="username" value="<?php echo posted_or_from_user('username'); ?>">
	</p>
	<p>
		<label for="email">Correo electrónico:</label>
		<input class="text-input" type="email" id="email" name="email" value="<?php echo posted_or_from_user('email'); ?>">
	</p>
	<p>
		<label for="name">Nombre:</label>
		<input class="text-input" type="text" name="name" id="name" value="<?php echo posted_or_from_user('name'); ?>">
	</p>
	<p>
		<label for="description">Descripción:</label>
		<textarea class="textarea" name="description" id="description"><?php echo posted_or_from_user('description'); ?></textarea>
	</p>
	<h2 class="section-title">Redes sociales (opcional)</h2>
	<p>
		<label for="url">Url personal:</label>
		<input type="url" class="text-input" id="url" name="url" value="<?php echo posted_or_from_user('url') ?>">
	</p>
	<p>
		<label for="twitter_user">Usuario de twitter: <span class="help-text">sin la arroba</span></label>
		<input class="text-input" type="text" name="twitter_user" id="twitter_user" value="<?php echo posted_or_from_user('twitter_user'); ?>">
	</p>

	<p>
		<label for="facebook_user">Usuario de facebook:</label>
		<input class="text-input" type="text" name="facebook_user" id="facebook_user" value="<?php echo posted_or_from_user('facebook_user'); ?>">
	</p>

	<p>
		<label for="gplus_id">Id de Google+:</label>
		<input class="text-input" type="text" name="gplus_id" id="gplus_id" value="<?php echo posted_or_from_user('gplus_id'); ?>">
	</p>

	<p class="submit">
		<input type="submit" value="Actualizar" class="btn btn-info">
	</p>
</form>
<!-- CAMBIAR CONTRASEÑA AQUÍ -->