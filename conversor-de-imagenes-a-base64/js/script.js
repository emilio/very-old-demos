(function(window, undefined){
	var $ = function(id){
			return document.getElementById(id)
		},
		imagePreviewDiv = $('image-preview'),
		resultDiv      = $('result'),
		imagePreview,
		backgroundPreview,
		support         = $('html5-support'),
		has_support     = ("File" in window && ("FileReader" in window || File.prototype.getAsDataURL )),
		has_fileReader  = ("FileReader" in window),
		readerCallback  = function(base64, is_image){
			resultDiv.style.display = "block"
			if( is_image ) {
				imagePreviewDiv.style.display = "block";
				imagePreview.src = base64;
				backgroundPreview.style.backgroundImage = "url(" + base64 + ")";
			}
			result.value = base64;
		},
		form            = $('form'),
		input           = $('archivo'),
		inputLabel      = document.querySelector ? document.querySelector('label[for="archivo"]') : null,
		secureCheck     = $('permitir'),
		result          = $('base64-result'),
		select = function( e ) { result.select(); };

	// Esto es global
	result.addEventListener ? result.addEventListener('click', select, false) : result.attachEvent('onclick', select);
	if( ! has_support ) {
		support.className += " no-support";
		support.innerHTML = "Tu navegador parece no soportar la funcionalidad necesaria para hacer una conversión rápida (aunque puedes convertirlos igualmente gracias a PHP).";
		return;
	} else {
		support.className += " has-support";
		support.innerHTML = "Tu navegador soporta las características necesarias para hacer una conversión al instante.";
	}

	backgroundPreview = imagePreviewDiv.querySelector('div');
	imagePreview = imagePreviewDiv.querySelector('img');

	form.addEventListener('submit', function(e){
		var reader,
			file = input.files[0],
			is_image = file && /^image/.test(file.type);

		resultDiv.style.display = imagePreviewDiv.style.display = "";

		e.preventDefault();

		if (!file)
			return;

		if( has_fileReader ){
			reader = new FileReader();
			reader.onload = function(){
				return readerCallback(reader.result, is_image);
			}
			reader.readAsDataURL(file);
		} else {
			return readerCallback(file.getAsDataURL(), is_image);
		}
	}, false)

	/*
	 * Bug en firefox (al hacer click en <label> no se abre el input)
	 */
	inputLabel.addEventListener('click', function(e){
		if( input.click ){
			e.preventDefault()
			input.click();
		}
	}, false)
})(window)