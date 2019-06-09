<?php
/**
 * Convertir un nombre de un feed en una id
 * @param string $name una cadena unicode
 * @return string la cadena escapada
 */
function nameToId($name) {
	// Limpiar los caracteres frecuentes de los nombres
	$replaces = array(
		' ' => '-',
		'@' => '-',
		'.' => '-',
		'(' => '',
		')' => '',
		'á' => 'a',
		'é' => 'e',
		'í' => 'i',
		'ó' => 'o',
		'ú' => 'u',
	);
	return str_replace(array_keys($replaces), array_values($replaces), mb_strtolower($name, 'UTF-8'));	
}

/**
 * Convertir un tiempo en una cadena legible
 * @param float $time
 * @return string el formato traducido
 */
function timeAgo($time) {
	$periods = array('segundo', 'minuto', 'hora', 'día', 'semana', 'mes', 'año', 'década');
	$lengths = array(60,60,24,7,4.35,12,10);

	$now = time();

	$difference = $now - $time;

	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}

	$difference = round($difference);

	if($difference != 1) {
		if( $periods[$j] !== 'mes' ) {
			$periods[$j].= 's';			
		} else {
			$periods[$j] .= 'es';
		}
	}

	return "Hace $difference $periods[$j]";
}

/** 
 * Obtener la url a un link
 * Básicamente para coger constancia de los links que clica el usuario
 *
 * @param string $url la url a registrar
 * @uses TRACK_CLICKS
 * @uses BASE_ABSOLUTE_URL
 * @return string la url lista para registrar o a secas si se ha decidido no hacerlo
 */
function getLink( $url ) {
	if( ! TRACK_CLICKS ) {
		return $url;
	}

	return BASE_ABSOLUTE_URL . 'redirect.php?url=' . urlencode($url);
}