<?php 
	session_start();

	// Si es `true` se mostrarán errores, y si es `false` no
	define('DEVELOPEMENT_MODE', true);

	// Abreviar DIRECTORY_SEPARATOR
	define('DS', DIRECTORY_SEPARATOR);

	// Definir los directorios
	define('BASE_PATH', dirname(__FILE__) . DS);



	/*
	 * Define otras constantes usadas en la aplicación aquí
	 */
	
	/** Expiración de las búsquedas (en segundos) */	
	define('SEARCHES_EXPIRATION_TIME', 60 * 60 * 24 * 5); // 5 días

	define('USER_IP', !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']));


	// Incluir el archivo que procesará la aplicación
	include BASE_PATH . 'app/start.php';