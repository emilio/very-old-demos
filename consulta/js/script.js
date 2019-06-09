(function() {
	var text = document.getElementById('text'),
		charcount = document.getElementById('text_charcount'),
		oninput = function oninput() {
			charcount.value = text.value.length;
		};

	if( 'oninput' in text ) {
		text.addEventListener('input', oninput, false);
	} else if (text.addEventListener) {
		text.addEventListener('keypress', oninput, false);
	} else {
		text.attachEvent('onkeypress', oninput);
	}

	if( ! text || ! charcount ) {
		return;
	}

}());