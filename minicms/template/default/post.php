<?php /** Este archivo se mostrará en los posts individuales */ ?>
<h1 class="post-title"><?php echo $post->title ?></h1>
<p class="post-datetime">Publicado el <?php echo date('d-m-Y', strtotime($post->date)); ?></p>
<p class="post-category">Categoría: <a href="<?php echo CMS\Url::to_category($post->category); ?>"><?php echo $post->category ?></a></p>
<div class="post-content">
	<?php echo $post->content ?>
</div>