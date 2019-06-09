(function(window, undefined){
	// Coger el formulario y comprobar por el soporte
	if( ! window.FormData )
		return (document.getElementById('support').innerHTML = "Tu navegador no soporta las características necesarias.")

	var form = document.querySelector('#form'),
		imageContainer = document.querySelector('#image-container');

	form.addEventListener('submit', onSubmit, false);

	function mostrarDatos(data){
		var output = "<figure class=\"image-item\">";

		if( ! data.hasError ){
			output += "<img src=\"" + data.imgSrc + "\" alt=\"Imagen recién subida\">";
			output += "<figcaption><strong>Url de la imagen: </strong>" + location.protocol + "//" + location.host + location.pathname.replace(/[^\/]*(\?|#).*/, '') + data.imgSrc + "</figcaption>";
		} else {
			output += "<p><strong>Ha habido un error: </strong></p>" + data.htmlResponse;
		}
		output += "</figure>";


		imageContainer.innerHTML += output;
	}

	function onSubmit(e){
		// Creamos la instancia del objeto formdata
		var data = new FormData(form),
			r = new XMLHttpRequest();

		// form.action = "upload.php"
		// Sólo es para evitar dolores de cabeza si alguien cambia el formulario
		r.open("POST", form.action, false);

		r.onreadystatechange = function(){
			if( r.readyState === 4 && r.status === 200 ){
				console.log( "Imagen subida. Respuesta: ", r.responseText )
				mostrarDatos( JSON.parse(r.responseText) );
			}
		}

		r.send(data);

		e.preventDefault();
	}
})(window)