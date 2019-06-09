<?php
/**
 * Aquí va toda la lógica para verificar el formulario enviado
 */

/**
 * Cualquier ocurrencia de las palabras separadas por |
 * @see has_forbidden_words();
 */
$forbidden_words_reg = '/\b(viagra|puta)\b/i';//Etc

/**
 * Cualquier conjunto de 1 a 15 caracteres que se repita al menos 20 veces seguidas
 * @see has_repetitive_chars();
 */
$repeated_words_reg = '/([\s\S]{1,15})\1{19,}/';

/**
 * Comprobar si un texto tiene palabras prohibidas
 * @param string $text el texto a comprobar
 * @return boolean
 */
function has_forbidden_words( $text ) {
	global $forbidden_words_reg;

	return preg_match($forbidden_words_reg, $text) !== 0;
}

/**
 * Comprobar si un texto tiene una gran cantidad de palabras o caracteres repetidos
 * @param string $text el texto a comprobar
 * @return boolean
 */
function has_repetitive_chars( $text ) {
	global $repeated_words_reg;

	return preg_match($repeated_words_reg, $text) !== 0;
}