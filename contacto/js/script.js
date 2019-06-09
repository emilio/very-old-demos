(function(window, document) {
	if( ! document.querySelectorAll ) {
		return false;
	}

	window.ec_form_messages = window.ec_form_messages || { error: {} };
	// Abreviar document.getElementById
	function $(id){
		return document.getElementById(id);
	}
	function log() {
		return window.console && console.log(arguments);
	}

	/*
		Variables para evitar acceder al DOM muchas veces,
		y abreviar
	*/
	var form = $('formulario'),
		error = $('error'),
		success = $('success'),
		inputs = form.querySelectorAll('input[name], textarea[name], select[name]'),
		elements = {},
		i = 0;

	for(; inputs[i]; i++) {
		elements[inputs[i].getAttribute('name')] = inputs[i];
	}

	function getValue(element) {
		switch(element.nodeName.toLowerCase()){
			case 'input':
				return element.getAttribute('type').toLowerCase() === "file" ? element.files[0] : element.value;
				// No hace falta break; (se ha acabado la funcion)
			case 'select': 
				return element.options[element.selectedIndex].value;
			case 'textarea':
			default:
				return element.value;
		}
	}

	function getElementsData() {
		var ret = {},
			i;
		// Creamos un objeto con los valores de cada elemento
		for( i in elements ) {
			if( elements.hasOwnProperty(i) ) {
				ret[i] = getValue(elements[i]);
			}
		}
		return ret;
	}

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
		var errores = [],
			valores = getElementsData(),
			hasFile;

		e = e || window.event;

		if( ! e.preventDefault ) {
			e.preventDefault = function() {
				e.returnValue = false;
			}
		}

		// comprobamos errores (prefiero mostrarlos normalmente quitando el required)
		if( estaVacio(valores.nombre)){
			errores.push(window.ec_form_messages.error.nombre);
		}
		if( ! emailVerification(valores.email)){
			errores.push(window.ec_form_messages.error.email);
		}
		if( estaVacio(valores.mensaje) ){
			errores.push(window.ec_form_messages.error.mensaje);
		}

		// Si hay errores no enviamos el formulario
		if(errores.length){
			error.innerHTML = '<ul><li>' + errores.join('</li><li>') + '</li></ul>';
			e.preventDefault();
			return false;
		}

		// Si no hay errores, ponemos la lista de errores vacíos
		error.innerHTML = '';

		hasFile = !! valores.adjunto;


		// Si no hay la tecnología necesaria para enviar el formulario con el archivo via AJAX,
		// Lo enviamos via HTTP (dejamos que se ejecute normalmente)
		if( hasFile && ! window.FormData ) {
			return true;
		}

		if( window.FormData ) {
			valores = new FormData(form);
		} else {
			valores = convertirObjeto(valores);
		}

		enviarform(valores, hasFile);
		
		e.preventDefault();

		return false;
	}

	// Función mediante la que enviámos el formulario
	function enviarform(data, hasFile){
		var request = new window.XMLHttpRequest();

		request.open("POST", '?ajax=true', true);
		if( ! hasFile && ! window.FormData ) {
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		}
		request.onreadystatechange = function(){
			var response;
			if( request.readyState === 4 ){
				response = JSON.parse(request.responseText);
				if( response.errors ){
					return error.innerHTML = "<ul><li>" + response.errors.join("</li><li>") + "</li></ul>"
				}
				// Si está todo correcto mostramos el mensaje y ocultamos el formulario
				correcto.innerHTML = window.ec_form_messages.correcto;
				form.style.display = "none";
			}
		}
		request.send(data);
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
	elements.nombre.required = elements.email.required = elements.mensaje.required = false;
	elements.email.type = "text";

	// Añadimos el evento cuando el formulario va a ser enviado
	form.addEventListener ? form.addEventListener('submit', onsubmit, false): form.attachEvent('onsubmit', onsubmit)

})(window, document, undefined)