<?php
	Format::add_shortcode('caption', function($args, $val) {
		extract(array_merge(array(
			'id'	=> '',
			'align'	=> '',
			'width'	=> '',
			'caption' => ''
		), $args));

		if( empty($caption) ) {
			$caption = trim(strip_tags($val));
			$val = str_replace($caption, '', $val);
		}

		if ( 1 > (int) $width || empty($caption) )
			return $val;

		return '<figure id="' . $id . '" style="width: ' . $width . 'px;">'. $val . '<figcaption class="caption-text">' . $caption . '</figcaption></figure>';
	});
	Format::add_shortcode('buttons', function($args) {
		extract(array_merge(array(
			'download' => '',
			'demo' => ''
		), $args));

		if( ! empty($demo) ) {
			$demo = '<a href="' . $demo . '" target="_blank" title="Link de descarga" class="boton">Demo</a>';
		}
		if( ! empty($download) ) {
			$download = '<a download href="' . $download . '" target="_blank" title="Link de descarga" download class="boton">Descarga</a>';
		}
		return "<p style='text-align: center;'>$demo $download</p>";
	});