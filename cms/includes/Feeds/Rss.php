<?php namespace Feeds;
use \Post, \App, \Config, \Url;
class Rss {
	public static function generate() {
		$posts = Post::where('status', '=', 'publish')->order_by('published_at','desc')->limit(10)->get();
		set_posts_meta($posts);
		$show_full_post = Config::get('feeds.fullpost');
		ob_start();
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo Config::get('site.title') ?></title>
		<link><?php echo Url::get() ?></link>
		<atom:link href="<?php echo Url::get('feed', 'rss'); ?>" rel="self" type="application/rss+xml" />
		<description><?php echo Config::get('site.description') ?></description>
		<language><?php echo Config::get('site.language') ?></language>
		<lastBuildDate><?php 
			$date = new \DateTime(date('c'));
			echo $date->format(/* \DateTime::RFC822 */ 'D, d M Y H:i:s O'); ?></lastBuildDate>
		<generator><?php echo 'BlogPress ' . App::version(); ?></generator>
		<webMaster><?php echo Config::get('site.webmaster_email'); ?> (<?php echo Config::get('site.webmaster_name'); ?>)</webMaster>
		<?php foreach ($posts as $post): set_current_post($post); ?>
		<item>
			<title><?php the_post_title() ?></title>
			<description><?php
			if($show_full_post){
				echo htmlspecialchars(get_post_content());
			} else {
				echo htmlspecialchars(htmlspecialchars(get_post_description())); // Doble: escape normal + el del feed
				echo htmlspecialchars('<br><a href="'. get_post_url() .'" title="'. get_post_title() .'">Leer completo &raquo;</a>');
			}
			echo htmlspecialchars('
				<br>
				<hr>
				<a href="' . get_post_url() . '" title="' . get_post_title() . '">' . get_post_title() . '</a> apareció primero en <a href="' . Url::get() . '" title="' . Config::get('site.description') . '">' . Config::get('site.title') . '</a>
			'); ?></description>
			<link><?php the_post_url() ?></link>
			<guid><?php the_post_url() ?></guid> 
			<comments><?php the_post_url() ?>#comments</comments>
			<author><?php the_post_author_meta('email') ?> (<?php the_post_author_meta('name'); ?>)</author>
			<category><?php the_post_category_name(); ?></category>
			<pubDate><?php $date = new \DateTime($post->published_at);
			echo $date->format(/* \DateTime::RFC822 */ 'D, d M Y H:i:s O'); ?></pubDate>
		</item>
		<?php endforeach; ?>
    </channel>
</rss><?php 
		$feed = ob_get_clean(); 
		return $feed;
	}
}