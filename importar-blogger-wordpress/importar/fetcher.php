<?php

header("Content-Type: text/html;charset=utf-8");
// Son operaciones largas, así que impediremos que se pare por el tiempo
set_time_limit (0);


$host = 'localhost'; // Nombre del host (si no lo sabes, deja localhost)
$nombredb = 'wordpress'; // Nombre que le hayas dado a la base de datos
$usuario = 'root'; // Usuario para la base de datos
$contrasena = 'root'; // Contraseña para ese usuario

$database_prefix = 'wp_'; // Si has usado otro prefijo que no sea "wp_", debes especificarlo aquí


$aprobar = 1;// Aprobar Comentarios Por defecto: 1=>sí, 0=>no

// Lista de autores
// Los autores DEBEN DE HABER SIDO CREADO DESDE WORDPRESS antes (ya que se requiere la ID).
// uso:
/* $autores = array(
	'nombre' => array(
		'wp_id' => ID DE USUARIO DE WORDPRESS,
		'url' => 'http://url/del/autor.html',
		'email' => 'emaildelautor@dominio.com'
	),
	'nombre2' = array( // ...
); */
/*
 * EJEMPLO PARA UN SÓLO AUTOR:
 */

/* $autores = array(
	'TU NOMBRE' => array(
		'wp_id' => 1,
		'url' => 'http://url/del/autor.html',
		'email' => 'emaildelautor@dominio.com'
	)
); */
$autores = array(
		'Emilio Cobos Álvarez' => array(
			'wp_id' => 1, // La id del usuario administrador (el primero que lo crea) es 1
			'url' => 'http://emiliocobos.net/',
			'email' => 'ecoal95@gmail.com'
		),
		'Autor 2' => array(
			'wp_id' => 2,
			'url' => 'http://urldeejemplo.com/',
			'email' => 'email@dominio.com'
		)
		//...
	);


/*
 * NO EDITAR A PARTIR DE AQUÍ
 */


//Conectamos con la base de datos
$pdo = new PDO("mysql:host=$host;dbname=$nombredb", $usuario, $contrasena);

$pdo->query("SET NAMES 'utf8'");


$current_post_ID = 0;
$current_comment_ID = 0;

$comments_table = $database_prefix . 'comments';
$posts_table = $database_prefix . 'posts';
$terms_table = $database_prefix . 'terms';
$term_taxonomy_table = $database_prefix . 'term_taxonomy';
$term_relationships_table = $database_prefix . 'term_relationships';

// Creamos un campo para la imagen de los comentarios
$pdo->query("ALTER TABLE `$comments_table` ADD `comment_author_image` VARCHAR(200) NOT NULL");

// Obtenemos los datos
$data = json_decode(isset($_GET['data']) ? $_GET['data'] : $_POST['data'] );

$insert_post_stmt = $pdo->prepare("INSERT INTO `$posts_table` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES 
															  (NULL, :post_author,   :fecha,      :fecha,          :content,       :title,     '',             :status,       'open',           'open',        '',              :name,       '',        '',       :modified,       :modified,           '',                      '0',           '',     '0',          'post',      '',               :comments)");

$insert_comment_stmt = $pdo->prepare("INSERT INTO `$comments_table` (`comment_ID`,`comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`, `comment_author_image`) VALUES (
																	NULL,        :postId,           :authorName,      :authorEmail,           :authorUrl,           '',                  :fecha,         :fecha,             :content,          '0',             :approved,          '',              '',             :parent,          :userId,   :authorImage)");

$tags = array();

$create_tag_stmt_1 = $pdo->prepare("INSERT INTO `$terms_table` (`name`, `slug`) VALUES (:name, :slug)");
$create_tag_stmt_2 = $pdo->prepare("INSERT INTO `$term_taxonomy_table` (`term_id`, `taxonomy`, `count`) VALUES (:id, 'post_tag', '0')");

$update_tag_count_stmt = $pdo->prepare("UPDATE `$term_taxonomy_table` SET `count` = `count` + 1 WHERE `term_id` = :id");
$create_relation_stmt = $pdo->prepare("INSERT INTO `$term_relationships_table` (`object_id`, `term_taxonomy_id`) VALUES (:post_id, :tax_id)");


function create_tag($name, $slug){
	global $tags, $create_tag_stmt_1, $create_tag_stmt_2, $pdo;
	$args = array(
		':name' => $name,
		':slug' => $slug
	);
	if( ! $create_tag_stmt_1->execute($args) ){
		echo "\nERROR CON TAG $name\n";
		print_r($create_tag_stmt_1->errorInfo());
	}
	$tag_id = $pdo->lastInsertId();

	$args = array(
		':id' => $tag_id
	);

	if ( ! $create_tag_stmt_2->execute($args) ){
		echo "\nERROR CON TAG $tag_id ($name)\n";
		print_r($create_tag_stmt_2->errorInfo());
	}
	$taxonomy_id = $pdo->lastInsertId();

	$tags[$name] = array(
		'tax_id' => $taxonomy_id,
		'ID' => $tag_id,
		'slug' => $slug
	);
	return $tags[$name];
}

function add_tag($post_id, $tag_name, $slug = false){
	global $tags, $update_tag_count_stmt, $create_relation_stmt;
	if( array_key_exists($tag_name, $tags) ){
		$tag = $tags[$tag_name];
	} else {
		$tag = create_tag($tag_name, $slug ? $slug: strtolower(str_replace(' ', '-',$tag_name )));
	}
	$update_tag_count_stmt->execute(array(
		':id' => $tag['ID']
	));
	$args = array(
		':post_id' => $post_id,
		':tax_id' => $tag['tax_id']
	);
	if( ! $create_relation_stmt->execute($args) ){
		echo "ERROR AL CREAR RELACIÓN ($post_id, $tag[tax_id])";
		print_r($create_relation_stmt->errorInfo());
	}
}


foreach ($data as $post) {
	if( ! is_object($post) ) continue;
	$name = is_string($post->name) ? $post->name : '';

	$fecha = date("Y-m-d H:i:s", strtotime($post->published));
	$modified = date("Y-m-d H:i:s", strtotime($post->updated));
	$content = ($post->content);
	$title = ($post->title);

	$author_id = 1; // Por defecto el autor principal
	if( isset($autores[$comment_author_name]) ) {
		$author_id = $autores[$comment_author_name]['wp_id'];
	}
	
	$args = array(
		':post_author' => $author_id,
		':fecha' => $fecha,
		':content' => $content,
		':title' => $title,
		':modified' => $modified,
		':name' => $name,
		':status' => $post->status,
		':comments' => $post->comments->length
	);
	if( ! $insert_post_stmt->execute($args) ){
		echo "\nERROR CON POST $current_post_ID\n";
		print_r($insert_post_stmt->errorInfo());
		print_r($pdo->errorInfo());
	}

	$current_post_ID = $pdo->lastInsertId();


	foreach ($post->tags as $tag) {
		add_tag($current_post_ID, $tag);
	}

	foreach ($post->comments as $comment) {
		if( ! is_object($comment) ) continue;
		writeComment($comment);
		$current_comment_ID = $pdo->lastInsertId();
		if( is_object($comment->replies) ){
			foreach ($comment->replies as $son_comment) {
				if( !is_object($son_comment) ) continue;
				writeComment($son_comment, true);
			}
		}
	}
}
echo "completado";
function writeComment($comment, $is_replie = false){
	global $insert_comment_stmt, $current_post_ID, $current_comment_ID, $aprobar, $autores;
	
	$user_id = 0;

	$comment_author_name = $comment->author->name;

	if( isset($autores[$comment_author_name]) ){
		$autor = $autores[$comment_author_name];

		$user_id = $autor['wp_id'];
		$comment->author->image = '';
		$comment->author->email = $autor['email'];
		$comment->author->uri = $autor['url'];
	}

	$fecha = date("Y-m-d H:i:s", strtotime($comment->published));
	$parent_id = $is_replie ? $current_comment_ID : 0;
	$args = array(
		':postId' => $current_post_ID,
		':authorName' => $comment->author->name,
		':authorEmail' => $comment->author->email,
		':authorUrl' => $comment->author->uri,
		':fecha' => $fecha,
		':content' => $comment->content,
		':approved' => $aprobar,
		':parent' => $parent_id,
		':userId' => $user_id,
		':authorImage' => $comment->author->image
		);
	if( ! $insert_comment_stmt->execute($args) ){
		echo "\nERROR CON COMENTARIO $current_comment_ID\n";
		print_r($insert_comment_stmt->errorInfo());
	}
}







?>