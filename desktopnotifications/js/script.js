/*
 * window.Notification polyfill
 * @author Emilio Cobos (http://emiliocobos.net)
 */

/* try prefixed */
if( ! window.Notification ) {
  window.Notification = (function() {
		return window.Notification || window.webkitNotification || window.mozNotification || window.oNotification || window.msNotification;
	})()
}

/* Request permission: */
if( window.Notification.permission === 'default' ) {
	window.Notification.requestPermission(/* function(permission) {} */);
}

/* If not standard (webkit first implementation) */
if( ! window.Notification && window.webkitNotifications ) {
	window.webkitNotifications.requestPermission();
	if( window.webkitNotifications.checkPermission() === 0 ) {
		window.Notification = (function(){
			var notifications_cache = {};
			return function(title, options) {
				var notification,
				tagNotifications,
				tagNotification;
				options = options || {};
				
				notification = window.webkitNotifications.createNotification(options.iconUrl || options.icon, title, options.body || '');

				if( options.onshow ) {
					notification.ondisplay = options.onshow;
				}

				if( options.onclick ) {
					notification.onclick = options.onclick;
				}

				if( options.onclose ) {
					notification.onclose = options.onclose;
				}

				if( options.onerror ) {
					notification.onerror = options.onerror;
				}

				if( options.tag ) {
					tagNotifications = notifications_cache[options.tag] = notifications_cache[options.tag] || [];
					while( tagNotification = tagNotifications.shift() ) {
						tagNotification.close();
					}
					notifications_cache[options.tag].push(notification);
				}

				return notification;
			}
		})()
	}
}
(function(){
	var hasSupport = !! window.Notification,
		supportDiv;
	
	// Comprobar soporte	
	if( ! hasSupport ) {
		supportDiv = document.getElementById('support') || { style: {}};
		supportDiv.style.display = "block";
		supportDiv.innerHTML = "Ups! Tu navegador no soporta las notificaciones de escritorio.";
		return;
	}

	document.getElementById('ahora').addEventListener('click', function(e){
		e.preventDefault();
		var notification = new Notification('Notificación de prueba', {
			body: 'Hola! Soy una notificación de escritorio que no se cerrará hasta que pulses el botón correspondiente.'
		});

		notification.show()
	});

	document.getElementById('delay').addEventListener('click', function(e){
		e.preventDefault();
			setTimeout(function(){
				new Notification('Notificación retardada y con icono', {
					body: 'Hey! ya han pasado 5 segundos',
					icon: 'img/icon.png',
					iconUrl: 'img/icon.png'
				})
				.show();
			}, 5000);
	});

	document.getElementById('auto-close').addEventListener('click', function(e) {
		e.preventDefault();
			var notification = new Notification('Notificación que se cerrará automáticamente', {
				body: 'Hey!, cuando pasen 2 segundos, me cerraré.'
			})
			notification.onshow = notification.ondisplay = function() {
				setTimeout(function() {
					notification.close();
				}, 2000);
			}
			notification.show()
	})
})()
