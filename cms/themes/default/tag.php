<h1><?php echo $tag->name ?></h1>
<?php foreach( $posts as $post ) {
	set_current_post($post);
	include 'post-index-and-category-template.php';
}