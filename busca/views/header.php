<?php if( ! isset($title)) {
	$title = '¿Y qué es?';
}?><!DOCTYPE html>
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
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<title><?php echo $title; ?></title>
	<meta property="og:title" content="<?php echo $title; ?>">

	<?php if(isset($description)): ?>
		<meta name="description" content="<?php echo $description ?>">
		<meta property="og:description" content="<?php echo $description ?>">
	<?php endif; ?>

	<link rel="canonical" href="<?php echo Url::current(); ?>">
	<meta property="og:url" content="<?php echo Url::current(); ?>">

	<meta property="og:site_name" content="¿Y qué es?">

	<?php Asset::print_styles('head'); ?>
	<?php Asset::print_scripts('head'); ?>
</head>
<body>
	<?php if( Url::get() === Url::current() ):
		// header home?>
		<header role="banner" class="header header-home">
			<h1 class="logo header-logo">¿Y qué es...?</h1>
		</header>
	<?php else: ?>
		<header role="banner" class="header header-<?php echo App::action() ?>">
			<h1 class="logo header-logo"><a href="<?php echo Url::get() ?>" rel="home">¿Y qué es...?</a></h1>
		</header>
	<?php endif; ?>
	<div class="main-container">