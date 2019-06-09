<div class="wrapper">
	<h2 class="section-title">Etiquetas</h2>
	<?php if(isset($success_msg)): ?>
		<div class="message message-info"><?php echo $success_msg ?></div>
	<?php endif; ?>
	<form action="<?php echo Url::get('admin@tags') ?>" method="POST">
		<button type="submit" value="autoclean" name="action" title="Eliminar etiquetas sin posts" class="btn btn-success btn-block">Autolimpieza de etiquetas</button>
	</form>
	<?php foreach ($tags as $tag): ?>
		<form action="<?php echo Url::get('admin@tags') ?>" method="POST" class="clearfix">
			<h3><?php echo $tag->name ?></h3>
			<p>Posts con esa etiqueta: <strong><?php echo Post_Tag::where('tag_id', '=', $tag->id)->count(); ?></strong></p>
			<p>
				<label for="tag-<?php echo $tag->id ?>-name">Nombre</label>: <input type="text" class="text-input" name="name" id="tag-<?php echo $tag->id ?>-name" value="<?php echo $tag->name ?>">
			</p>
			<p>
				<label for="tag-<?php echo $tag->id ?>-slug">Slug</label>: <input type="text" class="text-input" name="slug" id="tag-<?php echo $tag->id ?>-slug" value="<?php echo $tag->slug ?>">
			</p>
			<p>
				<label for="tag-<?php echo $tag->id ?>-description">Descripción</label>: 
				<textarea class="textarea" name="description" id="tag-<?php echo $tag->id ?>-description"><?php echo htmlspecialchars($tag->description) ?></textarea>
			</p>
			<p class="submit">
				<input type="hidden" name="id" value="<?php echo $tag->id ?>">
				<input type="submit" value="Actualizar" class="btn btn-info pull-rigth">
			</p>
		</form>
	<?php endforeach; ?>
</div>