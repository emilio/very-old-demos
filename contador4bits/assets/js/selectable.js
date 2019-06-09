// Este script nos deja seleccionar regiones de las celdas de karnaugh
document.addEventListener('click', function(e) {
	var t = e.target;
	if (t.nodeName === 'TD') {
		if( t.classList.contains('h1') && t.classList.contains('h2') ) {
			t.classList.remove('h1');
			t.classList.remove('h2');
		} else if( t.classList.contains('h1') ) {
			t.classList.remove('h1');
			t.classList.add('h2');
		} else if( t.classList.contains('h2') ) {
			t.classList.add('h1');
		} else {
			t.classList.add('h1');
		}
	};
}, false)