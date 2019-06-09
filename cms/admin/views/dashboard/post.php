<?php if( ! isset($post) ) {
	$post = (object) array(
		'title' => Param::post('title'),
		'content' => Param::post('content'),
		'slug' => Param::post('slug'),
		'type' => Param::get('type') ? Param::get('type') : 'post',
		'status' => 'draft',
		'description' => '',
		'format' => Param::post('format') ? Param::post('format') : 'html',
		'category_id' => Param::post('category') ? Param::post('category') : 1,
		'tags' => Param::post('tags') ? 
			array_map(
				'trim', 
				array_filter(
					explode(',' ,Param::post('tags'))
				)
			)
 : array(),
		'id' => Param::post('id') ?  Param::post('id') : 0
	);

	set_current_post($post);
}
if( isset($errors) ):
	foreach ($errors as $error): ?>
		<div class="message message-error span10 offset1"><?php echo $error ?></div>
	<?php endforeach; 
endif; ?>
<form action="<?php echo Url::get('admin@post') ?>" class="edit-form" method="POST">
	<p class="span10 offset1">
		<label for="title" class="assistive-text">Título:</label>
			<input autocomplete="off" type="text" name="title" class="post-title" placeholder="Inserta tu título aquí" value="<?php the_post_title(); ?>" id="title">
	</p>

	<p class="span10 offset1">
		<label for="slug" class="assistive-text">Url:</label>
		<span class="site-url"><?php echo Url::get() ?></span><input autocomplete="off" type="text" class="post-slug" id="slug" name="slug" value="<?php echo $post->slug ?>">
		<?php if( $post->status === 'publish' ): ?>
			<a href="<?php echo Url::get(null, $post->slug); ?>">Ver</a>
		<?php endif; ?>
	</p>

	<p class="span10 offset1 post-content-wrapper">
		<label for="content">Contenido del post</label>

		<input type="radio" name="format" value="markdown" id="format-markdown"<?php if($post->format === 'markdown') { echo ' checked';} ?>>
		<label for="format-markdown">Markdown</label>
		<input type="radio" name="format" value="html" id="format-html"<?php if($post->format === 'html') { echo ' checked';} ?>>
		<label for="format-html">HTML</label>

		<a id="preview" class="icon icon-eye icon-preview" href="#" title="Previsualizar"></a>
		<a id="zen-mode" class="icon icon-fullscreen icon-zen-mode" href="#" title="Entrar en el modo zen"></a>
			<textarea name="content" id="content" class="post-content"><?php echo htmlspecialchars($post->content); ?></textarea>
	</p>



	<!-- <h2 class="other-options-title">Otras opciones</h2> -->
	<p class="span10 offset1 post-description-wrapper">
		<label for="description">Descripción</label>
		<textarea class="post-description" placeholder="Introduce aquí la descripción" name="description" id="description"><?php echo htmlspecialchars($post->description); ?></textarea>
	</p>
	<div class="other-options span10 offset1 row">
		<div class="option option-decorated">
			<h5 class="other-options-title">Categoría</h5>
			<?php foreach (Category::all() as $category): ?>
			<p>
				<input type="radio" name="category_id" value="<?php echo $category->id ?>" id="category_<?php echo $category->id ?>"<?php if($post->category_id == $category->id) {echo ' checked';} ?>>
				<label for="category_<?php echo $category->id ?>"><?php echo $category->name ?></label>
			</p>
			<?php endforeach; ?>
			<a href="#" id="add-category" class="create-category-link">Crear categoría nueva</a>
		</div>
		<div class="option option-decorated">
			<h5 class="other-options-title">Etiquetas</h5>
			<label for="tags" class="assistive-text">Etiquetas (separadas por coma)</label>
			<input name="tags" id="tags" value="<?php foreach ($post->tags as $tag) {
				echo (is_string($tag) ? $tag : $tag->name) . ',';
			} ?>">
		</div>
		<div class="option">
			<p class="submit span10 offset1 row">
				<input type="hidden" name="id" value="<?php echo $post->id ?>">
				<input type="hidden" name="type" value="<?php echo $post->type ?>">
				<?php if( $post->status === 'draft' ): ?>
					<button type="submit" name="status" value="publish" class="btn btn-block btn-success">Publicar</button>
					<button type="submit" name="status" value="draft" class="btn btn-block btn-info">Guardar</button>
				<?php else: ?>
					<button type="submit" name="status" value="publish" class="btn btn-block btn-success">Actualizar</button>
					<button type="submit" name="status" value="draft" class="btn btn-block btn-info">Guardar en borradores</button>
				<?php endif; ?>

				<?php if( $post->id !== 0 ): ?>
					<button type="submit" name="action" value="delete" class="btn btn-block btn-error">Eliminar</button>
				<?php endif; ?>
			</p>
		</div>
	</div>


</form>