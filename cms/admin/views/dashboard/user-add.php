<form action="<?php echo Url::get('admin@add_user') ?>" method="POST" class="wrapper">
	<?php if( Param::get('ajax') !== 'true' ): ?>
		<h2 class="section-title">Crear un usuario</h2>
	<?php endif; ?>

	<?php if( isset($error) ): ?>
	<div class="message message-error"><?php echo $error ?></div>
	<?php endif; ?>
	<p>
		<label for="username">
			Nombre de usuario
		</label>
		<input type="text" class="text-input" required pattern="[A-Za-z0-9_]{3,}" name="username" id="username" placeholder="justinbieber88" value="<?php echo Param::post('username') ?>">
	</p>
	<p>
		<label for="name">Nombre</label>
		<input type="text" class="text-input" required name="name" id="name" placeholder="Introduce un nombre" value="<?php echo Param::post('name') ?>">
	</p>
	<p>
		<label for="email">Correo electrónico</label>
		<input type="email" class="text-input" required name="email" id="email" placeholder="pepe@dominio.tld" value="<?php echo Param::post('email') ?>">
	</p>
	<p>
		<label for="password">Contraseña</label>
		<input type="password" class="text-input" required name="password" id="password">
	</p>
	<p>
		<label for="password_verification">Contraseña (repetir)</label>
		<input type="password" class="text-input" required name="password_verification" id="password_verification">
	</p>
	<p>
		<label for="role">Rol</label>
		<select name="role" id="role">
			<option value="publisher">Publicador</option>
			<?php if( Auth::userCan('create_users') ): ?>
				<option value="admin">Administrador</option>
			<?php endif; ?>
		</select>
	</p>
	<p class="submit">
		<input type="submit" value="Crear" class="btn btn-success">
	</p>
</form>