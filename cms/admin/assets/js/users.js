EC.require(['DOM'], function() {
	var form_shown = false;
	EC.DOM.select('#add-user').on('click', function(e) {
		e.preventDefault();
		if( ! form_shown ) {
			form_shown = true;
			EC.require(['request'], function() {
				EC.request.get(AppData.adminUrl + 'add_user/?ajax=true', function(form) {
					console.log(arguments)
					EC.DOM.select('.users-controls').after(
						EC.DOM.create('div', {
							className: 'ajax-create-user-form-wrapper'
						}).append(form)
					)
				})
			});
		}
	});


	EC.DOM.select('.user-delete').on('click', function(e) {
		if( ! confirm('¿Seguro de que quieres borrar este usuario? Se borrarán todas sus entradas!') ) {
			e.preventDefault();
		}
	})
})