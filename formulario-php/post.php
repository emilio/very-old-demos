<?php
	// Aquí el email al que queres recibir el correo
	$receptor = "ecoal95@gmail.com";

	//-------------------------------------------------------------------
	// VARIABLES DEL MENSAJE

	$mensaje = preg_replace('/\n/','<br>',urldecode($_POST['mensaje']));
	$nombre = urldecode($_POST['nombre']);
	$email = urldecode($_POST['email']);
	$fecha = date('c');
	$titulo = "Nuevo mensaje de $nombre desde el formulario de contacto";



	// Comprobamos que hay un email váldo y un mensaje
	if( ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! isset($_POST['mensaje']) )
		die("ERROR");


	// El mensaje
	$data = "
	<html><head>
		<title>$titulo</title>
		<meta name='viewport' content='width:device-width,initial-scale=1'>
		<style>
		body{
			font-family:Arial,Helvetica,sans-serif;
			background: #eee;
			color: #333;
		}
		.mensaje {
			width: 100%;
			max-width: 650px;
			background: #fff;
			border: 1px solid #ccc;
			margin: 2em auto;
			padding: 2em;
		} </style>
	</head>
	<body>
		<div class='mensaje'>
			<h1>Nuevo mensaje de $nombre</h1>
			<p><strong>Fecha:</strong> $fecha</p>
			<p><strong>Mensaje:</strong><br>$mensaje</p>
			<p><strong>Email:</strong> <a href='mailto:$email'>$email</a></p>
		</div>
	</body></html>";

	$cabeceras = "MIME-Version: 1.0\r\n";
	$cabeceras .= "Content-type: text/html; charset=utf-8\r\n";
	$cabeceras .= "From: $nombre<$email>\r\n";
	$cabeceras .= "To: $receptor";

	// Enviamos nuestro email y damos cuenta sy hay algún error
	if(mail($receptor, $titulo, $data, $cabeceras, '-f yosoy@emiliocobos.net'))
		echo "SUCCESS";
	else 
		echo "ERROR";
?>