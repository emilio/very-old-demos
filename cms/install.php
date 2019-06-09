<?php
session_start();
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS );
include 'app/Autoloader.php';

EC\Autoloader::add_namespace('EC', BASE_PATH . 'app/');
EC\Autoloader::register();
EC\Config::init();
include EC\Config::get('path.includes') . 'core-functions.php';
/**
 * Execute a .sql file
 * @see exec_mysql_sentences
 */
function exec_mysql_file($file) {
	$content = file_get_contents($file);
	return exec_mysql_sentences($content); 
}

/**
 * Parses a number of sql sentences and executes them one by one
 * @param string $content the file contents/list of sentences
 */
function exec_mysql_sentences($content) {
	/** shabby way for eliminating comments (we don't remove the slash-asterisc comments, so we can't use them in the files) */
	$content = preg_replace('/(--|#).*/', '', $content);

	/** REALLY shabby way for separating queries. Since any of our queries has the semicolon char as text, it works, but... we shouldn't do it */
	$queries = array_filter(explode(';',$content));
	foreach ($queries as $query) {
		/** Exec the sentence and stop if error */
		try {
			EC\Database\DB::$db->query($query);
		} catch (PDOException $e) {
			return false;
		}
	}
	return true;
}

/**
 * Know if table exists in the database
 * @param string $table the table name
 * @return boolean
 */
function table_exists($table) {
	$result = EC\Database\DB::$db->query('SHOW TABLES LIKE \'' . $table . '\'')->fetch();
	return $result !== false && count($result) > 0;
}

/**
 * Know if site is installed 
 * @see table_exists
 */
function site_installed() {
	return table_exists('authors');
}

function display_database_details_form() {
	?>
	<h1>Bienvenido a la instalación de BlogPress <small>Versión <?php echo blogpress_version(); ?></small></h1>

	<form action="" class="span6 offset3 well" method="post">
		<h3>Paso 1: <small>Introduce los detalles de la conexión de la base de datos</small></h3>
		<p>
			<label for="driver">Tipo de bases de datos</label>
			<select name="driver" id="driver">
				<?php foreach (PDO::getAvailableDrivers() as $driver): ?>
					<option value="<?php echo $driver ?>"><?php echo $driver ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="hostname">Nombre del host</label>
			<input required type="text" value="<?php echo isset($_POST['hostname']) ? $_POST['hostname'] : 'localhost'; ?>" name="hostname" id="hostname">
		</p>
		<p>
			<label for="dbname">Nombre de la base de datos</label>
			<input required type="text" value="<?php echo @$_POST['dbname'] ?>" name="dbname" id="dbname">
		</p>
		<p>
			<label for="user">Nombre de usuario de la base de datos</label>
			<input type="text" autocomlete="off" value="<?php echo @$_POST['user'] ?>" name="user" id="user">
		</p>
		<p>
			<label for="password">Contraseña</label>
			<input type="password" autocomlete="off" value="" name="password" id="password">
		</p>
		<p class="submit">
			<input type="submit" value="Adelante!" class="btn btn-primary">
		</p>
	</form>
	<?php
}

function check_database_details() {
	EC\Database\DB::config($_POST);
	try {
		EC\Database\DB::connect();
	} catch( PDOException $e ) {
		?>
		<div class="alert alert-error">Los datos que has introducido no son válidos para la base de datos.</div>
		<?php
		return;
	}

	EC\Config::setPermanently('database', $_POST);
	if( ! EC\Config::savePermanently() ): ?>
		<div class="alert alert-error">Hubo un problema al guardar la configuración. Es necesario que des permisos de escritura al archivo <code>config.php</code> (<a href="?step=permissions">comprobar permisos</a>).</div>
		<?php return;
	endif;

	return EC\HTTP\Redirect::to($_SERVER['PHP_SELF'] . '?step=2');
}

function display_site_details_form() {
	?>
	<form action="" class="span6 offset3 well" method="post">
		<h3>Paso 2: <small>Introduce los detalles del sitio</small></h3>
		<p>
			<label for="title">Título del sitio</label>
			<input required type="text" value="<?php echo @$_POST['title'] ?>" name="title" id="title">
		</p>
		<p>
			<label for="description">Descripción del sitio</label>
			<textarea required name="description" id="description"><?php echo @$_POST['description'] ?></textarea>
		</p>
		<p>
			<label for="webmaster_email">Correo electrónico del administrador del sitio</label>
			<input type="email" autocomlete="off" value="<?php echo @$_POST['webmaster_email'] ?>" name="webmaster_email" id="webmaster_email">
		</p>
		<p>
			<label for="webmaster_name">Nombre del administrador del sitio</label>
			<input type="text" autocomlete="off" value="<?php echo @$_POST['webmaster_name'] ?>" name="webmaster_name" id="webmaster_name">
		</p>
		<p>
			<label for="language">Idioma del sitio</label>
			<select name="language" id="language">
				<?php foreach (EC\Config::get('available_languages') as $lang => $text ): ?>
					<option value="<?php echo $lang ?>"><?php echo $text ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p><span class="label label-warning">Atención:</span> Los datos del administrador se usarán en los feeds, no tiene mayor importancia, aunque tal vez en un futuro sirva para dar notificaciones.</p>
		<p class="submit">
			<input type="submit" value="Adelante!" class="btn btn-primary">
		</p>
	</form>
	<?php
}

function check_site_details() {
	$fields = array(
		'title', 
		'description',
		'webmaster_email',
		'webmaster_name'
	);
	foreach ( $fields as $field ):
			if( ! isset($_POST[$field]) || empty($_POST[$field]) ): ?>
			<div class="alert alert-error">No has rellenado todos los campos</div>
			<?php return;
			endif;
	endforeach;

	if( ! filter_var($_POST['webmaster_email'], FILTER_VALIDATE_EMAIL) ): ?>
		<div class="alert alert-error">El correo del administrador debe de ser válido</div>
		<?php return;
	endif;

	EC\Config::setPermanently('site', $_POST);
	if( ! EC\Config::savePermanently() ): ?>
		<div class="alert alert-error">Hubo un problema al guardar la configuración. Es necesario que des permisos de escritura al archivo <code>config.php</code> (<a href="?step=permissions">comprobar permisos</a>).</div>
		<?php return;
	endif;


	return EC\HTTP\Redirect::to($_SERVER['PHP_SELF'] . '?step=3');
}

function display_user_form() {
	?>
	<form action="" class="span6 offset3 well" method="post">
		<h3>Paso 3: <small>Introduce datos para crear tu usuario</small></h3>
		<p>
			<a class="btn btn-block btn-info" href="<?php echo $_SERVER['PHP_SELF'] ?>?step=wpimport">Espera! quiero importar desde WordPress</a>
		</p>
		<p>
			<label for="name">Nombre</label>
			<input required type="text" value="<?php echo @$_POST['name'] ?>" name="name" id="name">
		</p>
		<p>
			<label for="username">Nombre de usuario</label>
			<input required type="text" name="username" id="username" value="<?php echo @$_POST['username'] ?>">
		</p>
		<p>
			<label for="password">Contraseña</label>
			<input required type="password" name="password" id="password" value="">
		</p>
		<p>
			<label for="password_verification">Contraseña (repetir)</label>
			<input required type="password" name="password_verification" id="password_verification" value="">
		</p>
		<p>
			<label for="email">E-mail</label>
			<input required type="email" name="email" id="email" value="<?php echo @$_POST['email'] ?>">
		</p>
		<p class="submit">
			<input type="submit" value="Adelante!" class="btn btn-primary">
		</p>
	</form>
	<?php
}

function check_user_details() {
	EC\Config::init();
	EC\Database\DB::config(EC\Config::get('database'));
	EC\Database\DB::connect();

	// Ya instalado... 
	if( ! site_installed() ) {
		if( false === create_db_structure() ) 
			return;
	}

	class User extends EC\Database\DBObject {
		public static $table = 'authors';
	}


	$fields = array(
		'username',
		'name',
		'password',
		'password_verification',
		'email'
	);
	foreach ( $fields as $field ):
		if( ! isset($_POST[$field]) || empty($_POST[$field]) ): ?>
		<div class="alert alert-error">No has rellenado todos los campos</div>
		<?php return;
		endif;
	endforeach;
	if( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ): ?>
		<div class="alert alert-error">El correo debe ser válido</div>
		<?php return;
	endif;
	if( $_POST['password'] !== $_POST['password_verification'] ): ?>
		<div class="alert alert-error">Asegúrate de que las contraseñas coinciden</div>
		<?php return;
	endif;
	unset($_POST['password_verification']);
	$_POST['password'] = EC\Auth\Hash::make($_POST['password']);

	if( User::where('1', '=', '1')->count() ): ?>
		<div class="alert alert-error">Ya existen usuarios, deberías de entrar con su contraseña.</div>
		<?php return;
	endif;
	User::create(array_merge($_POST, array(
		'role' => 'superadmin',
		)));
	echo 'Usuario creado!';
	EC\HTTP\Redirect::to($_SERVER['PHP_SELF'] . '?step=finished');
}

function create_db_structure() {
	$file = 'create.sql';
	if( ! file_exists($file) ): ?>
		<div class="alert alert-error">No se ha encontrado el archivo de instalación (<code>create.sql</code>). Revisa la instalación</div>
	<?php return false;
	endif;
	
	if( false === exec_mysql_file($file) ) {
		?><div class="alert alert-error">Problema de SQL. Error: <?php echo $e->getMessage() ?></div><?php 
		return;
	}
}

function display_thanks() {
	?>
	<h2>Gracias, has instalado BlogPress!</h2>
	<p>Tu sitio está listo. Ahora puedes borrar éste archivo (<code>install.php</code>), y <a href="admin/">probar tu nuevo panel de administración.</a></p>
	<p>Esperemos que disfrutes!</p>
	<?php
}

function display_wpimport_screen() {
	EC\Database\DB::config(EC\Config::get('database'));
	EC\Database\DB::connect();
	?>
	<div class="span8 offset2">
		<h2>Importación desde WordPress</h2>
		<p>La importación desde wordpress debe ser hecha desde el principio, para no perder las relaciones entre los elementos de la base de datos.</p>
		<p>Para ello, <strong>la base de datos que tenemos que usar será la de wordpress</strong> (o una copia).</p>
		<p>Si no lo has hecho ya, vuelve al <a href="<?php echo $_SERVER['PHP_SELF'] ?>">comienzo del instalador</a> e introduce los datos adecuados.</p>
		<p>Si sí lo has hecho, símplemente escoge:</p>
		<form action="" class="well" method="post">
			<p>
				<label for="delete" class="checkbox">
					<input type="checkbox" name="delete" id="delete" value="true"> Quiero que se borre toda la instalación de wordpress existente</label>
			</p>
			<p>
				<label for="table_prefix">Prefijo de las tablas</label>
				<input type="text" name="table_prefix" id="table_prefix" value="wp_">
			</p>
			<p class="submit">
				<input type="submit" class="btn btn-primary" value="Vamos allá!">
			</p>
		</form>
	</div>
	<?php
}

function import_from_wp() {
	EC\Database\DB::config(EC\Config::get('database'));
	EC\Database\DB::connect();
	$delete_wp = isset($_POST['delete']) && $_POST['delete'] === 'true';
	$prefix = $_POST['table_prefix'];

	if( ! site_installed() ) {
		if( false === create_db_structure() ): ?>
			<div class="alert alert-error">Ha habido algún error desconocido al importar.</div>
		<?php return ;endif;
	}

	if( ! table_exists($prefix . 'posts') ) :?>
		<div class="alert alert-error">No hemos encontrado una instalación de wordpress válida en la base de datos</div>
	<?php return ;endif; 

	if( ! file_exists('wordpress-import.sql') ): ?>
		<div class="alert alert-error">No existe el archivo <code>wordpress-import.sql</code>. Revisa la instalación</div>
	<?php return ;endif; 

	$content = file_get_contents('wordpress-import.sql');
	if( $prefix !== 'wp_' ) {
		$content = str_replace('wp_', $prefix, $content);
	}
	if( false === exec_mysql_sentences($content) ): ?>
		<div class="alert alert-error">Ha habido algún error desconocido al importar.</div>
	<?php return ;endif;

	if( $delete_wp ) {
		$content = file_get_contents('wordpress-delete.sql');
		if( $prefix !== 'wp_' ) {
			$content = str_replace('wp_', $prefix, $content);
		}
		if( false === exec_mysql_sentences($content) ): ?>
			<div class="alert alert-error">Ha habido algún error desconocido al importar.</div>
		<?php return ;endif;
	}
	return EC\HTTP\Redirect::to($_SERVER['PHP_SELF'] . '?step=wpimportfinished');
}


function display_wpimport_finished_screen() {
	?>
	<div class="span8 offset2 well">
		<h2>Has finalizado la importación desde wordpress</h2>
		<p><strong>Nota importante:</strong> Los nombres de usuarios son los de WordPress, pero todos los usuarios tienen la contraseña <code>temp</code>, por lo que deberán cambiarla desde el panel de control lo antes posible (puedes hacerlo tú ahora mismo por ellos).</p>
		<p>Esperemos que te diviertas :)</p>
	</div>
	<?php
}

function display_permissions_page() {
	$writable_files = array(
		'config.php',
		'uploads',
		'storage',
		'storage/cache',
	);
	?>
	<div class="span8 offset2 well">
		<h2>Permisos de escritura necesarios:</h2>
		<table class="table table-stripped">
			<thead>
				<th>Archivo</th>
				<th>Permiso actualmente</th>
			</thead>
			<tbody>
				<?php foreach ($writable_files as $file): ?>
				<tr>
					<td><?php echo $file ?></td>
					<?php if(is_writable($file)): ?>
						<td class="text-success">Sí</td>
					<?php else: ?>
						<td class="text-error">No</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
ob_start();
?>
<!DOCTYPE html>
<!--[if lt IE 7 & (!IEMobile)]>
<html class="ie ie6 lt-ie7 lt-ie8 lt-ie9 no-js">
<![endif]-->
<!--[if (IE 7) & (!IEMobile)]>
<html class="ie ie7 lt-ie8 lt-ie9 no-js">
<![endif]-->
<!--[if (IE 8) & (!IEMobile)]>
<html class="ie ie8 lt-ie9 no-js">
<![endif]-->
<!--[if IE 9 & (!IEMobile)]>
<html class="ie ie9 no-js">
<![endif]-->
<!--[if (gt IE 9) | (IEMobile) | !(IE)  ]><!-->
<html class="no-js">
<!--<![endif]-->
<head prefix="og: http://ogp.me/ns# article: http://ogp.me/ns/article#">
	<meta charset="utf-8">

	<title>Instalación de Blogpress</title>

	<meta content="noindex, nofollow" name="robots">

	<link rel="stylesheet" href="admin/assets/_css/bootstrap.css">
	<script src="js/modernizr.js"></script>
</head>
<body>
	<div class="container">
		<div class="row">
		<?php
		switch(@$_GET['step']) {
			case 'wpimport':
				if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					import_from_wp();
				}
				display_wpimport_screen();
				break;
			case 'wpimportfinished':
				display_wpimport_finished_screen();
				break;
			case 'permissions': 
				display_permissions_page();
				break;
			case 'finished':
				display_thanks();
				break;
			case '3':
				if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					check_user_details();
				}
				display_user_form();
				break;
			case '2':
				if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					check_site_details();
				}
				display_site_details_form();
				break;
			case '1':
			default:
				if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

					check_database_details();
				}
				display_database_details_form();
				break;
		}
		?>
		</div>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>