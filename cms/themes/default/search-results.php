<?php if( ! count($results) ): ?>
	Lo siento, no se ha encontrado ningún post :(
<?php else: 
	foreach( $results as $post ) {
		set_current_post($post);
		include 'post-index-and-category-template.php';
	}
endif; ?>