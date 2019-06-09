<?php 

Event::on('error.404', function() {
	Header::status(404);
}, 10);

/**
 * Delete post cache when post is published
 */
Event::on('post.publish', function($e) {
	$post_id = $e['id'];
	$keys = array(
		'sitemap',
		'rss_feed',
		'post_' . $post_id . '_tags',
		'post_' . $post_id . '_content_filtered',
	);

	foreach ($keys as $key) {
		Cache::delete($key);
	}
});
Event::on('db.connect_error', function($e) {
	echo '<h1>No se pudo conectar con la base de datos</h1><pre>' . $e->getMessage() . '</pre>';
	die();
});

Event::on('blog.head_end', function($e) {
	?>
	<link rel="canonical" href="<?php echo Url::current() ?>">
	<meta name="generator" content="Blogpress <?php echo blogpress_version() ?>">
	<link rel="alternate" type="application/rss+xml" href="<?php echo Url::get('feed', 'rss'); ?>" title="<?php echo Config::get('site.title') . ' (Feed RSS)'; ?>">
	<?php if( View::exists('search') ): ?>
		<link rel="search" href="<?php echo Url::get('search') ?>">
	<?php endif; ?>
	<?php if( has_previous_post() ): ?>
		<link rel="prev" href="<?php the_previous_post_url() ?>">
	<?php endif; ?>
	<?php if( has_next_post() ): ?>
		<link rel="next" href="<?php the_next_post_url() ?>">
	<?php endif; ?>
	<?php
});