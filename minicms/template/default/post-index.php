<?php /** Este archivo se mostrará en el index y las categorías */ ?>
<h2 class="post-title"><a href="<?php echo Url::to_post($post->slug); ?>"><?php echo $post->title ?></a></h2>
<p class="post-datetime">Publicado el <?php echo date('d-m-Y', strtotime($post->date)); ?></p>
<p class="post-category">Categoría: <a href="<?php echo CMS\Url::to_category($post->category); ?>"><?php echo $post->category ?></a></p>
<div class="post-content post-excerpt">
	<p><?php echo substr(strip_tags($post->content), 0, 45) . '...'; // Un resumen de 45 caracteres ?></p>
</div>