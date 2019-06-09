(function(window, document){
	// Abreviar document.getElementById
	function $(id){
		return document.getElementById(id);
	}

	/*
		Variables para evitar acceder al DOM muchas veces,
		y abreviar
	*/
	var form = $('formulario'),
		nombre = $('nombre'),
		email = $('email'),
		asunto = $('asunto'),
		mensaje = $('mensaje'),
		error = $('error'),
		correcto = $('correcto');

	// Comprobar e-mail y cadena vacía
	function emailVerification(valor){
		// Expresión regular para validar el email (http://stackoverflow.com/questions/46155/validate-email-address-in-javascript)
		// Yo había hecho una propia, pero no estoy en mi ordenador, y esta parece funcionar bien
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
		
		return regex.test(valor);
	}
	function estaVacio(valor){
		return valor === "";
	}

	// Función que sucederá cada vez que el formulario se envía
	function onsubmit(e){
		var data = {
			nombre: nombre.value,
			email: email.value,
			asunto: asunto.options[asunto.selectedIndex].value,
			mensaje: mensaje.value
		},
		errores = [];

		e = e || window.event;

		// Evitamos redireccionar
		if( typeof e.preventDefault === 'function'){
			e.preventDefault()
		} else {
			e.returnValue = false;
		}

		// comprobamos errores (prefiero mostrarlos normalmente quitando el required)
		if( estaVacio(data.nombre)){
			errores.push("El campo del nombre no puede estar vacío")
		}
		if( ! emailVerification(data.email)){
			errores.push("Introduce un email válido")
		}
		if( estaVacio(data.mensaje) ){
			errores.push("El mensaje no puede estar vacío")
		}

		// Si hay errores no enviamos el formulario
		if(errores.length){
			error.innerHTML = '<ul><li>' + errores.join('</li><li>') + '</li></ul>';
			return false;
		}

		// Si no hay errores, ponemos la lista de errores vacíos
		error.innerHTML = '';


		// Enviamos el formulario, pero evitamos redireccionar
		enviarform(data);

		return false;
	}

	// Función mediante la que enviámos el formulario
	function enviarform(data){
		var request = createRequest(),
		params = convertirObjeto(data);

		request.open("POST", 'post.php', false);
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.onreadystatechange = function(){
			if( request.readyState === 4 ){
				if( request.responseText !== "SUCCESS" ){
					return error.innerHTML = "<ul><li>Hubo un error al enviar el formulario</li></ul>"
				}
				// Si está todo correcto mostramos el mensaje y ocultamos el formulario
				correcto.innerHTML = "El mensaje se envió correctamente, intentaré responderte lo antes posible";
				form.style.display = "none";
			}
		}
		request.send(params);
	}

	// Creamos una solicitud AJAX
	function createRequest(){
		// IE7 no implementó bien XMLHttpRequest, así que intentamos usar el nativo
		if( window.ActiveXObject ){
			try{
				return new window.ActiveXObject("Microsoft.XMLHTTP"); 
			} catch(ex){
				alert("Usas un navegador demasiado antiguo (IE5), actualízalo para poder desfrutar de la web")
				return null
			}
		}
		return new window.XMLHttpRequest();
	}

	// Convierte un objeto en una cadena de texto preparada para ser enviada al servidor
	function convertirObjeto(obj){
		var ret = '',
		key, current = 0;
		for (key in obj){
			ret += ((current === 0 ? '' : '&') + key + '=' + encodeURIComponent(obj[key]) );
			current++
		}
		return ret;
	}

	/*
		Si no está javascript activado y es un navegador moderno, el navegador comprobará los campos por nosotros
		Si sí lo está, prefiero comprobarlos y mostrar los errores en conjunto.
	*/
	nombre.required = email.required = mensaje.required = false;
	email.type = "text";


	// Añadimos el evento cuando el formulario va a ser enviado
	form.addEventListener ? form.addEventListener('submit', onsubmit, false): form.attachEvent('onsubmit', onsubmit)





})(window, document, undefined)