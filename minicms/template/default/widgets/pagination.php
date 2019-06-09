<?php
// No hay varios posts
if( $page->type !== 'index' ) return; 

if( $page->subtype === 'category' ) {
	$count = count($database->categories->{$page->category_name});
} else {
	$count = count($database->posts); 
} 

// No hay suficientes posts para paginar
if( $count < $page->posts_per_page ) return;?>
<!-- por implementar -->