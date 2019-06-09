<?php
	$uploads_dir = 'imagenes/';
	$respuesta = null;
	/*
	 * Comprobamos si hay algún error en el envío
	 */
	if( $_SERVER['REQUEST_METHOD'] !== 'POST' ):
		// Devolvemos
		$respuesta = array(
				'hasError' => true,
				'error' => 'mala_solicitud',
				'htmlResponse' => '<p>La solicitud no ha sido enviada correctamente :(</p>'
			);
	// Si no hay imagen o el archivo enviado no es una imagen...
	elseif( ! $_FILES || ! isset( $_FILES['imagen'] ) ):
		$respuesta = array(
				'hasError' => true,
				'error' => 'no_hay_archivo',
				'htmlResponse' => '<p>Tienes que adjuntar un archivo.</p>'
			);
	else:
		$imagen = $_FILES['imagen'];
		$nombre = $imagen['name'];
		// Extensión del archivo
		$extension = pathinfo($nombre, PATHINFO_EXTENSION);

		// El archivo no es una imagen
		if( strpos($imagen['type'], 'image') === false || preg_match("/^(png|jpe?g|bmp|gif)$/", $extension) === 0 ):
			$respuesta = array(
				'hasError' => true,
				'error' => 'mal_tipo_de_archivo',
				'htmlResponse' => '<p>El archivo tiene que ser una imagen!</p>'
			);
		// Si la imagen es más grande que 300 kb, no la guardamos
		elseif( $imagen['size'] > 300 * 1024 ):
			$respuesta = array(
				'hasError' => true,
				'error' => 'imagen_muy_grande',
				'htmlResponse' => '<p>Has subido una imagen muy grande (más de 300kb). Por favor, prueba con una más pequeña ;)</p>'
			);
		// Todo bien!
		else:
			// Creamos un nombre de archivo con la fecha y un número aleatorio para evitar duplicados
			$nombre = 'temp_' . md5(time()) . '_' . rand();

			$nombre = $uploads_dir . $nombre . '.' . $extension;

			// Lo movemos
			move_uploaded_file($imagen['tmp_name'], $nombre);

			$respuesta = array(
					'hasError' => false,
					'error' => null,
					'htmlResponse' => '<img src="' . $nombre .  '" alt="imagen subida">',
					'imgSrc' => $nombre
				);
		endif;
	endif;

	// Limpiar otros archivos si son más antiguos de 5 minutos
	require_once('clean.php');

	echo json_encode($respuesta);
