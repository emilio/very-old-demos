<?php 
/** Si no hay url o la url no es válida no hacemos nada */
if( ! isset($_GET['url']) || ! filter_var($url = $_GET['url'], FILTER_VALIDATE_URL) ) {
	die();
}

/** Añadir la url y un salto de línea al log */
file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'log.txt', $url . "\r\n", FILE_APPEND | EX_LOCK);

/** Redirección 301 para que no afecte a la url receptora */
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $url, true, 301);