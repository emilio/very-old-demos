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

	<title><?php echo $page->title; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<meta property="og:title" content="<?php echo $page->title; ?>">
	<?php if( isset($page->description) ): ?>
		<meta name="description" content="<?php echo $page->description; ?>">
		<meta property="og:description" content="<?php echo $page->description; ?>">
	<?php endif; ?>

	<?php if( isset($page->url) ): ?>
		<link rel="canonical" href="<?php echo $page->url; ?>">
		<meta property="og:url" content="<?php echo $page->url; ?>">
	<?php else: ?>
		<meta name="robots" content="noindex, nofollow">
	<?php endif; ?>

	<?php if( $page->paged > 0 ): ?>
		<meta name="robots" content="noindex, nofollow">
	<?php endif; ?>

	<meta property="og:site_name" content="<?php echo $site->name ?>">

	<link rel="stylesheet" href="<?php echo CMS\Url::to_template('css/style.css'); ?>">
	<script src="<?php echo CMS\Url::to_template('js/modernizr.js'); ?>"></script>
</head>
<body>
	<header class="site-header">
		<h1 class="site-title"><a href="<?php echo $site->url ?>" rel="home"><?php echo $site->name ?></a></h1>
		<p class="site-description"><?php echo $site->description ?></p>
	</header>
	<div class="container">

