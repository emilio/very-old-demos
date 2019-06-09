<?php
/**
 * Funciones para formatear el texto
 */

/**
 * Reemplazo masivo de caracteres y palabras, siempre que no estén rodeados de otro caracter
 * @see correct_syntax();
 */
$syntax_replacements = array(
	"xq" => "por qué",
	"x" => "por",
	"q" => "que",
	// Etc...
);

/**
 * Corregir la sintaxis de un texto
 * @param string $text el texto
 */
function correct_syntax( $text ) {
	global $syntax_replacements;

	// Conjunto de caracteres para escapar los saltos de línea
	// Importante: que no contenga espacios ni "/"
	$linebreak_placeholder = '__LINEBREAK__PLACEHOLDER__';

	// Unificar saltos de línea
	$text = str_replace("\r\n", "\n", $text);

	// Eliminar espacios al comienzo del texto y al final
	$text = trim($text);

	// Reemplazar caracteres concretos
	// Forma 1:
	// foreach ($syntax_replacements as $expresion => $reemplazo) {
	// 	$text = preg_replace("/\b" . $expresion . "\b/", $reemplazo, $text);
	// }
	// Forma 2:
	$text = preg_replace_callback("/\b(" . implode('|', array_keys($syntax_replacements)) . ")\b/", function($matches) use ($syntax_replacements) {
		return $syntax_replacements[$matches[1]];
	}, $text);

	// Dobles espacios, manteniendo saltos de línea
	$text = str_replace("\n", $linebreak_placeholder, $text);
	$text = preg_replace("/\s+/", " ", $text);

	// Dobles preguntas, comas...
	$text = preg_replace("/(\[|\(|,|¿|\?|¡|!|\)|\])\\1+/", "$1", $text);
	
	// Espacios tras comas, puntos y comas y dos puntos
	$text = preg_replace("/(,|;|:\))(\S)/", "$1 $2", $text);

	// Reemplazar por un punto
	$text = preg_replace("/(?<!\.)\.\.(?!\.)|\.\s\.|\.,|,\./", ".", $text);

	// Reemplazar más de 4 puntos por 3 puntos
	$text = preg_replace("/\.{4,}/", '...', $text);

	// Interrogación punto
	$text = preg_replace("/\?+\.+/", "?", $text);

	// Espacios antes de paréntesis, interrogaciones y demás
	$text = preg_replace("/(\S)(\(|¿|¡|\[|\])/", "$1 $2", $text);

	// Eliminar espacios posteriores a cierto signo
	$text = preg_replace("/(\(|¿|¡)\s/", "$1", $text);

	// Eliminar espacios anteriores a cierto signo
	$text = preg_replace("/\s(,|:|;|!|\?|\))/", "$1", $text);

	// Letras (o números) junto a aperturas de pregunta/exclamación
	$text = preg_replace("/([A-Za-z0-9])(¡|¿)/","$1 $2" ,$text);

	// Letra sin espacio tras punto o exclamación, y hacer mayúsculas todas
	$text = preg_replace_callback("/(\.|\?|!)\s?([A-Za-z])/", function( $matches ) {
		return $matches[1] . ' ' . strtoupper($matches[2]);
	}, $text);

	// Mayúsculas tras nueva línea y eliminación de espacios al comienzo de línea
	$text = preg_replace_callback("/($linebreak_placeholder+|^)(\s+)?([A-Za-z])/", function( $matches ) {
		return $matches[1] . strtoupper($matches[3]);
	}, $text);

	// Punto al final del texto
	$text = preg_replace("/([A-Za-z0-9\)])$/","$1." ,$text);

	// Restauras saltos de línea (también son encontrados por \s, por eso los escapamos antes)
	$text = str_replace($linebreak_placeholder, "\n", $text);
	return $text;
}