<?php
	// Aquí el email al que queres recibir el correo
	$receptor = "ecoal95@gmail.com";

	$mensajes_error = array(
		'email' => 'Introduce un e-mail válido',
		'mensaje' => 'Tienes que introducir un mensaje',
		'nombre' => 'Introduce un nombre'
	);

	$mensaje_correcto = "El mensaje se envió correctamente, intentaré responderte lo antes posible";

	$enviado = $_SERVER['REQUEST_METHOD'] === "POST" || isset($_GET['ajax']);
	if( $enviado ) {
		include('message.php');
	}
?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="ie6 lt-ie7 lt-ie8 lt-ie9 no-js" lang="es">
<![endif]-->
<!--[if IE 7]>
<html class="ie7 lt-ie8 lt-ie9 no-js" lang="es">
<![endif]-->
<!--[if IE 8]>
<html class="ie8 lt-ie9 no-js" lang="es">
<![endif]-->
<!--[if (gte IE 9) | !(IE)  ]><!-->
<html class="no-js" lang="es">
<!--<![endif]-->
	<head>
		<!-- Arriba: hacks para IE ;) -->
		<title>Formulario de contacto</title>
		<!--
			Charset (juego de caracteres para que se vean las tildes, etc)
		-->
		<meta charset="UTF-8">

		<!-- 
			Viewport (la usan los móviles para calcular el ancho de las páginas)
		-->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<!--
			Referencia a nuestros archivos CSS
		-->
		<link rel="stylesheet" type="text/css" href="css/reset.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<!--
			Referencia a modernizr (en el head)
			script.js => encima de </body>, 
						 para acelerar la carga 
						 y evitar conflictos con el DOM
		-->
		<script type="text/javascript" src="js/modernizr.js"></script>

	</head>
	<body>
		<div class="container" id="container">

			<!--
				Aquí pondremos los errores (de existir alguno) 
			-->
			<div id="error"><?php if( $enviado && $errores !== null ) {
				echo '<ul><li>' . implode('</li><li>', $errores) . '</li></ul>';
			} ?></div>

			<!--
				Definimos el método de envío (POST hace que nuestros datos no se puedan ver desde
				el navegador, que es lo más seguro), y la URL a la que enviarlo (post.php)
			-->
			<form id="formulario" name="formulario" method="POST" action="" enctype="multipart/form-data">
				<!--
					El nombre
				-->
				<p>
					<label for="nombre">Nombre</label><span class="requerido">*</span>
					<input type="text" name="nombre" id="nombre" size="30" required placeholder="Nombre">
				</p>
				<!--
					El e-mail
				-->
				<p>
					<label for="email">e-mail</label><span class="requerido">*</span>
					<input type="email" name="email" id="email" placeholder="Introduce tu e-mail" required>
				</p>
				<!--
					El asunto
				-->
				<p>
					<label for="asunto">Asunto</label><span class="requerido">*</span>
					<select id="asunto" name="asunto" required>
						<option value="Pedir un tutorial">Pedir un tutorial</option>
						<option value="Ayuda con diseño web">Ayuda con diseño web</option>
						<option value="Problema con el nuevo diseño">Problema con el nuevo diseño</option>
						<option value="Demo que no funciona">Demo que no funciona</option>
						<option value="Decir hola">Decir hola</option>
						<option value="Otro">Otro</option>
					</select>
				</p>
				<!--
					El mensaje
				-->
				<p>
					<label for="mensaje">Mensaje</label><span class="requerido">*</span>
					<textarea id="mensaje" name="mensaje" placeholder="El asunto" required></textarea>
				</p>
				<p>
					<label for="asunto">Agregar un archivo <small>(opcional)</small></label>
					<input type="file" name="adjunto" id="adjunto"></input>
				</p>
				<p class="submit">
					<input type="submit" id="submit" value="Enviar">
				</p>
			</form>
			<!--
				Aquí pondremos el mensaje cuando enviemos el mensaje
			-->
			<div id="correcto"><?php if( $enviado && $errores === null ) {
				echo $mensaje_correcto;
			} ?></div>
		</div>
		<script>
			window.ec_form_messages = {
				correcto: "<?php echo $mensaje_correcto ?>",
				error: <?php echo json_encode($mensajes_error) ?>
			}
		</script>
		<script type="text/javascript" src="js/script.js"></script>
	</body>
</html>