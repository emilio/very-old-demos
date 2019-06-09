<!DOCTYPE html>
<html>
<head>
	<?php
		/**
		 * Aquí puedes incluir todo lo que quieras que valla en el <head>
		 * Para incluir scripts y estilos, se recomienda usar en functions.php la clase `Asset`
		 * Es importante que esté al final la función page_head(), que añadirá tanto scripts como estilos, más las etiquetas necesarias de los feeds
		 */
	?>
	<meta charset="UTF-8">
	<title><?php the_page_title(); ?></title>
	<meta name="description" content="<?php the_page_description() ?>">
	<?php page_head(); ?>
</head>
<body>
	<div class="container">