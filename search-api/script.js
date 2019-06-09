(function () {
    'use strict';
	// Tu API Key
	var KEY = "",
		// La ID de tu motor de búsqueda
		ENGINE_ID = "",
		// Número de resultados
		NUM_RESULTS = 5,
		// Calculamos la url que servirá de base
		BASE_URL = "https://www.googleapis.com/customsearch/v1?key=" + KEY + "&cx=" + ENGINE_ID,
		// Cogemos el elemento del DOM
		input = document.getElementById('search-input'),
		// Mandar datos a la consola
		log = function (datos) {
			return window.console && window.console.log(datos);
		},
		loadScript = function (url, opciones) {
            var fjs = document.getElementsByTagName('script')[0],
                js = document.createElement('script'),
                opciones = opciones || {},
                callbackName = "gsearchCB_" + +new Date();
            
            window[callbackName] = function (data) {
                log(data);
                if( opciones.callback ) {
                    opciones.callback.call(null, data);
                }
                js.parentNode.removeChild(js);
                window[callbackName] = null;
            }
            
            js.src = url + "&callback=" + callbackName;
            js.type = "text/javascript";
            js.async = true;
            
            js.onerror = function () {
                js.parentNode.removeChild(js);
                window[callbackName] = null;
                opciones.error && opciones.error();
            }
            
            fjs.parentNode.insertBefore(js, fjs);
            
            return true;
		},
		results,
		timer;

	if (!input) // Si no hay elemento del DOM, no podemos hacer nada
		return;

	// Evitamos autocompletar
	input.setAttribute('autocomplete', 'off');

	function search(query, count, startIndex) {
		var url = BASE_URL + "&q=" + query,
			count = count || NUM_RESULTS; 

		// Hecho por si implementaba paginación
		if( startIndex )
			url += "&start=" + startIndex;

		// Lo mismo, si no usamos número de items por defecto
		url += "&num=" + count;


		loadScript(url, {
            error: function(){
                results.innerHTML = "<p>Ha habido algún error, probablemente el límite haya sido excedido, prueba mañana</p>";
            },
            callback: function(data) {
                results.innerHTML = parse(data)
            }
        })

    }
	function parse(data){
		// Obtenemos los datos
		var totalResults = parseInt(data.searchInformation.totalResults, 10),
			// Los resultados en sí
			items = data.items,
			// Sólo un contador
			i = 0,
			// El número de items
			len = items ? items.length : 0,
			// Variable para usar en el loop que recorrerá los ítems
			item,
			// La salida en HTML
			output;

		log("Resultados de la consulta:")
		log(data);

		// Si no hay nada, lo decimos y salimos
		if( ! items )
			return "<p>No se ha encontrado nada</p>";

		// Si hay algo, mostramos el número total de resultados
		output = "<p class=\"total-results\">" + totalResults + " resultados</p><ul>";

		for( ; i < len; i++ ){
			item = items[i];
			// Mostramos un título, una url y un resumen por cada resultado obtenido
			output += "<li><h3><a title=\"" + item.snippet.replace(/[<>"]/g, function(a){ return {"\"": "&quot;", "<": "&lt;", ">": "&gt;"}[a]}) + "\" href=\"" + item.link + "\">" + item.htmlTitle + "</a></h3>";

			output += "<small>" + item.htmlFormattedUrl + "</small><p class=\"snippet\">" + item.htmlSnippet + "</p></li>";
			output += "</li>"
		}


		output += "</ul>";

		return output;
	}

	/*
	 * Cuando levantamos una tecla del teclado, se ejecuta ésta función,
	 * que deja un intervalo de 0.5 segundos, y luego busca
	 */
	function onKeyUp(e){
		// log("Keyup");

		// Evitar que se solicite cada vez que tocas el teclado
		if( timer ) {
			clearTimeout(timer);
		}
		timer = setTimeout(function(){
			// Si hemos escrito tres caracteres o menos todavía no hay nada que mostrar
			if( input.value.length <= 3 )
				return;

			// Si no hay elemento donde mostrar los resultados, lo creamos
			if( !results ){
				results = document.createElement('div');
				results.className = "goog-ajax-results";
				input.parentNode.appendChild(results);
			}
			results.innerHTML = "<p>Cargando...</p>";
			// Buscamos
			search(encodeURIComponent(input.value));
		}, 500);
	}

	// Añadimos el evento
	input.addEventListener ?
		input.addEventListener('keyup', onKeyUp, false): input.attachEvent('onkeyup', onKeyUp);
})()
