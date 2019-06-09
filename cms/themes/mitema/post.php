<?php set_current_post($post); ?>
<article class="post post-<?php echo $post->id ?>" id="post-<?php echo $post->id ?>">
	<header>
		<h2 class="post-title"><a href="<?php the_post_url() ?>" rel="bookmark" title="<?php echo htmlentities($post->title) ?>"><?php echo $post->title ?></a></h2>
		<time class="post-published"><?php echo $post->created_at ?></time>
		<h3 class="post-author">
			<a href="<?php the_post_author_url() ?>" rel="author" title="Ver el perfil de <?php echo $post->author->name ?>">
				<?php echo $post->author->name ?>
			</a>
		</h3>
	</header>
	<div class="post-content">
		<?php the_post_content() ?>
	</div>
	<footer class="post-footer">
		<?php if(count($post->tags)): ?>
			<div class="tags">
				<h6>Etiquetas</h6>
				<?php foreach ($post->tags as $tag): ?>
					<a href="<?php the_tag_url($tag) ?>"><?php echo $tag->name ?></a>
				<?php endforeach ?>
				<?php post_admin_links() ?>
			</div>
		<?php endif ?>
	</footer>
</article>
