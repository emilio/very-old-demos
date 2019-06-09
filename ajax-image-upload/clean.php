<?php
	if( isset($uploads_dir) )
		$directorio_para_limpiar = $uploads_dir;
	else
		$directorio_para_limpiar = 'imagenes/';

	$archivos = glob($directorio_para_limpiar . '*.*'); // Queremos comprobar todos los archivos

	// $archivo será el nombre del archivo
	foreach ($archivos as $archivo) {
		$fecha_de_modificacion = filectime($archivo);

		// El tiempo desde la modificación en segundos
		$tiempo_desde_modificacion = time() - $fecha_de_modificacion;

		// Si han pasado más de 5 minutos
		if( $tiempo_desde_modificacion > 5 * 60 ){
			// Borramos el archivo
			unlink($archivo);
		}
	}
