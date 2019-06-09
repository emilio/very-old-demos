<?php
	/**
	 * notice that this file is not neccesary:
	 * it's included from index.php, tag.php and category.php to avoid code duplication
	 */
?>
<article class="post post-<?php echo $post->id ?>" id="post-<?php echo $post->id ?>">
	<header>
		<h2 class="post-title"><a href="<?php the_post_url() ?>" rel="bookmark" title="<?php the_post_title() ?>"><?php the_post_title() ?></a></h2>
		<time class="post-published"><?php post_published_date() ?></time>
		<h3 class="post-author">
			<a href="<?php the_post_author_url() ?>" rel="author" title="Ver el perfil de <?php the_post_author_name() ?>">
				<?php the_post_author_name() ?>
			</a>
		</h3>
	</header>
	<div class="post-content">
		<p><?php the_post_excerpt() ?></p>
	</div>
	<footer class="post-footer">
		<a href="<?php the_post_url() ?>" class="read-more" title="Leer el artículo completo">Leer más</a>
	</footer>
</article>