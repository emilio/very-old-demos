<form class="wrapper" method="POST" action="<?php echo Url::get('admin@comment') ?>">
	<?php if( Param::get('edited') === 'true' ): ?>
		<div class="message message-success">El comentario se editó correctamente</div>
	<?php endif; ?>
	<h2 class="section-title">Editar el comentario</h2>
	<p>
		<label for="author_name">Nombre</label>
		<input class="text-input comment-author-name" type="text" name="author_name" id="author_name" value="<?php echo $comment->author_name ?>">
	</p>
	<p>
		<label for="author_email">Correo electrónico</label>
		<input class="text-input comment-author-email" type="email" name="author_email" id="author_email" value="<?php echo $comment->author_email ?>">
	</p>
	<p>
		<label for="author_url">Url</label>
		<input class="text-input comment-author-url" type="url" name="author_url" id="author_url" value="<?php echo $comment->author_url ?>">
	</p>
	<p>
		<label for="content">Contenido</label>
		<textarea class="comment-content textarea" name="content" id="content"><?php echo htmlspecialchars($comment->content) ?></textarea>
	</p>
	<p class="submit">
		<input type="hidden" name="id" value="<?php echo $comment->id ?>">
		<?php if( $comment->approved ): ?>
			<button class="btn btn-success" type="submit" name="approved" value="1">Guardar</button>
			<button class="btn btn-warning" type="submit" name="approved" value="0">Pasar a moderación</button>
		<?php else: ?>
			<button class="btn btn-success" type="submit" name="approved" value="1">Guardar y aprobar</button>
			<button class="btn btn-warning" type="submit" name="approved" value="0">Guardar manteniéndolo en moderación</button>
		<?php endif; ?>
			<button class="btn btn-error pull-rigth" id="delete-comment" type="submit" name="delete[]" value="<?php echo $comment->id ?>">Borrar permanentemente</button>
	</p>
</form>