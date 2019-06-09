<?php
	define('BASE', __DIR__);
	define('RECAPTCHA_PUBLIC_KEY', '');
	define('RECAPTCHA_PRIVATE_KEY', '');

	$recaptcha_error = null; // Podríamos comentar está línea
	ini_set('display_errors', 'On');



	include 'inc/recaptchalib.php';

	if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		include 'inc/verification/verify.php';
		include 'inc/verification/form-verification.php';
		$error = verify_form();
		if( Error::is($error) ) {
			// Mostrar página con errores
			include 'inc/header.php';
			include 'inc/error.php';
			include 'inc/main.php';
			include 'inc/footer.php';
		} else {
			include 'inc/process/process.php';
			// Enviar y mostrar mensaje de éxito
			include 'inc/header.php';
			if( send_form_data() ) {
				include 'inc/valid.php';
			} else {
				include 'inc/mail-error.php';
				include 'inc/main.php';
			}
			include 'inc/footer.php';
		}
		// Se sale
		exit;
	}

include 'inc/header.php'; ?>

		<?php if( isset($_GET['show']) && $_GET['show'] === 'tos' ): ?>
			<?php include 'inc/tos.php'; ?>
		<?php else: ?>
			<?php include 'inc/main.php'; ?>
		<?php endif; ?>

<?php include 'inc/footer.php'; ?>
