<?php 

/**
 * Simple class for error handling
 * @author Emilio Cobos <ecoal95@gmail.com>
 */
class Error {
	public $errors = array();
	public function add($field, $error_type) {
		if( ! isset($this->errors[$field]) ) {
			$this->errors[$field] = array();
		}
		$this->errors[$field][] = $error_type;
	}
	public function has($field = null, $type = null) {
		if( $field === null && $type === null ) {
			return count($this->errors) !== 0;			
		}
		if( $type === null ) {
			return isset($this->errors[$field]);
		}
		return in_array($type, $this->get($field));
	}
	public function get($field = null) {
		if( $field === null ) {
			return $this->errors;
		}
		if( $this->has($field) ) {
			return $this->errors[$field];
		}
		return array();
	}
	public static function is($error) {
		return $error instanceof Error;
	}
}

/**
 * Validate the form
 * @return Error|boolean
 */
function verify_form() {
	$error = new Error; 
	$required_fields = array( 'title', 'sex', 'age', 'text', 'recaptcha_challenge_field', 'recaptcha_response_field' );

	// campos requeridos
	foreach( $required_fields as $field) {
		if( ! isset($_POST[$field]) || empty($_POST[$field]) ) {
			if( $field === 'recaptcha_challenge_field' || $field === 'recaptcha_response_field' ) {
				$field = 'recaptcha';
			}
			$error->add($field, 'empty');
		}
	}

	// Si el título no tiene 20 caracteres al menos...
	if( strlen($_POST['title']) <= 20 ) {
		$error->add('title', 'length');
	// El título tiene palabras prohibidas
	} else if( has_forbidden_words($_POST['title']) ) {
		$error->add('title', 'forbidden');
	}

	// Si el contenido no tiene 700 caracteres al menos...
	if( strlen($_POST['text']) <= 700 ) {
		$error->add('text', 'length');
	// Si tiene palabras prohibidas...
	} else if( has_forbidden_words($_POST['text']) ) {
		$error->add('text', 'forbidden');
	// Si tiene caracteres repetidos 20 veces al menos...
	} else if( has_repetitive_chars($_POST['text']) ) {
		$error->add('text', 'repetitive');
	}

	// Si el valor para el sexo no es reconocido
	if( ! $error->has('sex') && ! in_array( $_POST['sex'], array( 'F', 'M' )) ) {
		$error->add('sex', 'unknown');
	}

	// Si la edad no cumple un formato adecuado...
	if( ! $error->has('age') && ! preg_match("/[0-9]+\s(meses|años)/", $_POST['age']) ) {
		$error->add('age', 'unknown');
	}

	// Recaptcha
/*	if( ! $error->has('recaptcha') ) {
		$recaptcha_response = recaptcha_check_answer(
			RECAPTCHA_PRIVATE_KEY,
			$_SERVER['REMOTE_ADDR'],
            $_POST['recaptcha_challenge_field'],
            $_POST['recaptcha_response_field']);

		if( ! $recaptcha_response->is_valid ) {
			$error->add('recaptcha', 'invalid');
			?><!-- <?php var_dump($recaptcha_response) ?> --><?php
		}
	}*/

	// Si hay errores los devolvemos, si no devolvemos true
	if( $error->has() ) {
		return $error;
	}

	return true;
}