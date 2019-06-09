<?php set_current_post($author->posts[0]) ?>
<h1><?php echo $author->name ?> <small>(<?php echo $author->username ?>)</small></h1>
<p class="author-description">
	<div class="author-image"><img src="http://gravatar.com/avatar/<?php echo md5(trim(strtolower($author->email))) ?>?s=50&amp;d=mm" alt="<?php echo $author->name ?>"></div>
	<?php echo $author->description ?>
</p>
<div class="author-latest-posts">
	<h2>Últimos artículos de <?php echo $author->name ?></h2>
	<?php foreach ($author->posts as $post): set_current_post($post);?>
		<article class="author-latest-post post post-<?php echo $post->id ?>">
			<h2 class="post-title"><a href="<?php the_post_url() ?>"><?php echo $post->title ?></a></h2>
			<time datetime="<?php echo date('c', strtotime($post->created_at)) ?>"><?php echo date('c', strtotime($post->created_at)); ?></time>
			<p class="resume"><?php the_post_excerpt() ?></p>
		</article>
		<?php endforeach ?>
</div>