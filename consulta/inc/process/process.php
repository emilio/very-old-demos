<?php 
include 'syntax.php';

function send_form_data() {
	$title = htmlspecialchars($_POST['title']);
	$text = htmlspecialchars(correct_syntax($_POST['text']));
	// $destinatario = 'ewrtyfs42@inmedicina.org'; 
	$destinatario = 'p.lopez.caceres@gmail.com'; 
	$asunto = '';
	$cuerpo = <<<EOT
	$title<br />
	$text<br />
EOT;
	if( isset($_POST['record']) && ! empty($_POST['record'])) {
		$antecedentes = htmlspecialchars($_POST['record']);
		$cuerpo .= <<<EOT
	<h3>Otros Antecedentes:</h3>
	<ul><li>$antecedentes</li></ul>
EOT;
	}
	$sexo = $_POST['sex'] === 'F' ? 'Femenino' : 'Masculino';
	// Esto en principio no sería necesario, pero lo hacemos por que es la única parte vulnerable del formulario que no podemos verificar adecuadamente
	// y alguien con muy mala idea y que conozca que ésta es la única posible vulnerabilidad...
	$cuerpo .= <<<EOT
	<h3>Sexo:</h3>
	<ul><li>$sexo</li></ul>
	<h3>Edad:</h3>
	<ul><li>$_POST[age].</li></ul>
EOT;
	$headers = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=utf-8\r\n"; 
	$headers .= "From: noresponder@inmedicina.org\r\n"; 
	$headers .= "Reply-To: noresponder@inmedicina.org\r\n"; 
	$headers .= "Return-path: noresponder@inmedicina.org\r\n";
	return @mail($destinatario,$asunto,$cuerpo,$headers, '-fyosoy@emiliocobos.net');
}