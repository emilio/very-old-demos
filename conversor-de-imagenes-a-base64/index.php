<?php
$uploaded_file = false;
$format_error = false;

if( $_FILES && isset($_FILES['archivo']) ):
	$uploaded_file = true;
	$format_error = false;

	$archivo = $_FILES['archivo'];
	$nombre = $archivo['name'];
	// Extensión del archivo
	$extension = pathinfo($nombre, PATHINFO_EXTENSION);
	// El archivo no es una imagen
	$is_image = ! (strpos($imagen['type'], 'image') === false || preg_match("/^(png|jpe?g|bmp|gif)$/", $extension) === 0 );

	$base64 = base64_encode(file_get_contents($archivo['tmp_name']));
	$base64 = 'data:' . $archivo['type'] . ';base64,' . $base64;
endif;

$transformed = ($uploaded_file && ! $format_error);

?><!DOCTYPE html>
<!--[if lt IE 7 & (!IEMobile)]>
<html class="ie ie6 lt-ie7 lt-ie8 lt-ie9">
<![endif]-->
<!--[if (IE 7) & (!IEMobile)]>
<html class="ie ie7 lt-ie8 lt-ie9">
<![endif]-->
<!--[if (IE 8) & (!IEMobile)]>
<html class="ie ie8 lt-ie9">
<![endif]-->
<!--[if IE 9 & (!IEMobile)]>
<html class="ie ie9">
<![endif]-->
<!--[if (gt IE 9) | (IEMobile) | !(IE)  ]><!-->
<html class="no-js">
<!--<![endif]-->
	<head prefix="og: http://ogp.me/ns#">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">

		<title>Conversor de imágenes (y todo tipo de archivos) a base64 | Emilio Cobos</title>
		<meta property="og:title" content="Conversor de imágenes (y todo tipo de archivos) a base64 | Emilio Cobos">

		<meta name="description" content="Convierte tus imágenes a Base64 fácilmente. También convierte todo tipo de archivos a base64. Artículo completo: http://emiliocobos.net/filereader-y-como-transformar-imagenes-a-base64-sin-subirlas-a-ningun-sitio/" lang="es">
		<meta property="og:description" content="Convierte tus imágenes a Base64 fácilmente. También convierte todo tipo de archivos a base64. Artículo completo: http://emiliocobos.net/filereader-y-como-transformar-imagenes-a-base64-sin-subirlas-a-ningun-sitio/">

		<link rel="canonical" href="http://emiliocobos.net/demos/conversor-de-imagenes-a-base64/">
		<meta property="og:url" content="http://emiliocobos.net/demos/conversor-de-imagenes-a-base64/">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:creator" content="@emiliocobos95">
		<meta name="twitter:site" content="@emiliocobos95">

		<meta name="author" content="Emilio Cobos Álvarez">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<!--[if lt IE 9]>
			<script src="js/html5.js"></script>
		<![endif]-->
	</head>
	<body class="just-container">
		<header role="banner">
			<h1>Conversor de imágenes a Base64</h1>
		</header>
		<section role="main">
			<article>
				<p class='support' id='html5-support'></p>
				<form enctype="multipart/form-data" action="" id="form" method="POST">
					<p><label for="archivo">Selecciona un archivo</label><input type="file" name="archivo" id="archivo"></p>
					<p><input type="submit" value="Convertir!"></p>
				</form>
				<div<?php if($transformed) echo ' style="display: block;"'?> class="preview" id="result">
					<h2>El código del archivo</h2>
					<textarea id='base64-result'><?php if( $transformed ) echo $base64; ?></textarea>
					<div class="image-preview"<?php if($transformed && $is_image) echo ' style="display:block"';?> id="image-preview">
						<h2>Vista previa de la imagen</h2>
						<img alt<?php if($transformed && $is_image) echo ' src="' . $base64 . '"' ?>>
						<pre><code>&lt;img src="data:image/[png|gif|jpg|jpeg]; base64, [...]" alt="Mi maravillosa imagen"&gt;</code></pre>
						<h2>Vista previa como fondo</h2>
						<div<?php if($transformed && $is_image) echo ' style="background-image: url(' . $base64 . ')'; ?>></div>
						<pre><code>div {
	background-image: url(data:image/[png|gif|jpg|jpeg]; base64, [...]);
}</code></pre>
					</div>
				</div>
			</article>
			<aside class="share" id="social">
				<div class="fb-like" data-href="http://emiliocobos.net/demos/conversor-de-imagenes-a-base64/" data-send="false" data-layout="box_count" data-width="100" data-show-faces="false"></div>
				<div class="g-plusone" data-size="tall"></div>
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://emiliocobos.net/demos/conversor-de-imagenes-a-base64/" data-via="emiliocobos95" data-lang="es">Twittear</a>
			</aside>
		</section>
		<footer role="contentinfo">
			<p>Hecho con cuidado por <a href="//emiliocobos.net" rel="author">Emilio Cobos</a>.</p>
		</footer>
		<script type="text/javascript" src="js/script.js"></script>
		<div id="fb-root"></div>
		<script type="text/javascript">
			window.___gcfg = {lang: 'es'};
			(function(w,d,s){
				var js, fjs = d.getElementsByTagName(s)[0], 
					load = function(url, id){
						js = d.createElement(s);
						if( ! d.getElementById(id)){
							js.type = "text/javascript";js.src = url; js.id = id;
							fjs.parentNode.insertBefore(js, fjs);
						}
					}
				function cargarTodo(){
					load("//apis.google.com/js/plusone.js","g_plusone");
					load("//connect.facebook.net/es_ES/all.js#xfbml=1", "facebook-jssdk");
					load("//platform.twitter.com/widgets.js","twitter-wjs");
				}
				w.addEventListener ? w.addEventListener("load" , cargarTodo, false) : w.attachEvent("onload", cargarTodo);
			})(window,document,"script")
		</script>
	</body>
</html>