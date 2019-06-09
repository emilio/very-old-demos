<!DOCTYPE html>
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
<head>
	<meta charset="utf-8">

	<title>Login | <?php echo Config::get('site.title') ?></title>
	<meta name="robots" content="noindex, nofollow">

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="stylesheet" href="<?php echo Url::asset('_css/bootstrap.css') ?>">
</head>
<body>
<div class="row">
	<div class="well span4 offset4">
		<?php if(isset($errors)):
		foreach($errors as $error): ?>
			<div class="alert alert-error"><?php echo $error; ?></div>
		<?php endforeach; endif; ?>

		<?php if( Param::get('loggedout') ): ?>
			<div class="alert alert-success">
				Has cerrado sesión
			</div>
		<?php endif; ?>
		<form action="<?php echo Url::get('admin@login') ?>" method="POST">
			<p>
				<label for="username">Usuario</label>
				<input id="username" class="input-block-level" name="username" type="text">
			</p>
			<p>
				<label for="password">Contraseña</label>
				<input id="password" class="input-block-level" name="password" type="password">
			</p>
			<p class="submit pull-left">
				<?php if( Param::request('redirect-to') ): ?>
					<input type="hidden" name="redirect-to" value="<?php echo Param::request('redirect-to'); ?>">
				<?php endif; ?>
				<input type="submit" class="submit btn" name="submit" value="Entrar">
			</p>
			<p class="password-lost pull-right"><a href="<?php echo Url::get('admin@password'); ?>">¿Has perdido la contraseña?</a></p>
		</form>
	</div>
</div>
</body>