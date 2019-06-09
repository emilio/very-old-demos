<?php header('Content-type: text/plain'); 

echo function_exists('mail') . "\n";

echo var_dump(@mail('ecoal95@gmail.com', 'mensaje de prueba', 'contenido del mensaje'));
