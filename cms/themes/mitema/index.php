<?php foreach( $posts as $post ): set_current_post($post); ?>
	<article class="post post-<?php echo $post->id ?>" id="post-<?php echo $post->id ?>">
		<header>
			<h2 class="post-title"><a href="<?php the_post_url($post) ?>" rel="bookmark" title="<?php echo htmlentities($post->title) ?>"><?php echo $post->title ?></a></h2>
			<time class="post-published"><?php echo $post->created_at ?></time>
			<h3 class="post-author">
				<a href="<?php the_post_author_url() ?>" rel="author" title="Ver el perfil de <?php echo $post->author->name ?>">
					<?php echo $post->author->name ?>
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
<?php endforeach ?>