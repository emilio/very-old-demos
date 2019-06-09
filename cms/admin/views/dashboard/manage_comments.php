<div class="wrapper">
	<h2 class="section-title">Comentarios</h2>
	<?php if( Param::get('count') && Param::get('action') ):
		$count = Param::get('count');
		switch (Param::get('action')) {
			case 'delete':
				$message = "Se han borrado correctamente $count comentarios";
				break;
			case 'moderate':
				$message = "Se mandaron a moderación $count comentarios";
				break;
			case 'approve': 
				$message = "Se aprobaron $count comentarios";
				break;
		} ?>
		<div class="message message-success"><?php echo $message ?></div>
	<?php endif; ?>
	<?php ob_start(); ?>
	<div class="pagination pagination-centered">
		<ul>
		<?php
			$from = 1;
			$to = ceil($total_comments / $per_page);
			for(; $from <= $to; $from++ ): ?>
				<li <?php if( $page === $from ) { echo 'class="active"'; } ?>><a href="<?php echo Url::get('admin@manage_comments', null, array_merge($_GET, array('page' => $from))) ?>"><?php echo $from ?></a></li>
		<?php endfor; ?>
		</ul>
	</div>
	<?php $pagination = ob_get_clean(); ?>
	<?php if( $total_comments === 0 || count($comments) === 0 ): ?>
		<div class="message message-error"><strong>Upps!</strong> No se han encontrado posts relacionados con tu consulta.</div>
	<?php else: ?>
		<?php echo $pagination; ?>
		<table class="table table-stripped table-bordered posts-table main-data-table">
			<thead>
				<tr>
					<th>Id</th>
					<th>Autor</th>
					<th>Contenido</th>
					<th>Fecha</th>
					<th>En respuesta a</th>
					<th>Entrada</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($comments as $comment): ?>
				<tr class="post-table-comment" data-comment-id="<?php echo $comment->id ?>">
					<td>
						<a href="<?php echo Url::get('admin@edit_comment', $comment->id) ?>"><?php echo $comment->id ?></a>
					</td>
					<td>
						<?php 
						if( (int) $comment->author_id !== 0):
							if( ! isset($comment_authors[$comment->author_id]) ) {
								$comment_authors[$comment->author_id] = User::get($comment->author_id);
							}
							$comment_author = $comment_authors[$comment->author_id];
							if( $comment_author ): ?>
								<a title="Comentarios de <?php echo $comment_author->name ?>" href="<?php echo Url::get('admin@manage_comments', null, array('page' => 1, 'author' => $comment_author->username)) ?>"><?php echo $comment_author->name; ?></a>
							<?php else: ?>
								<?php echo $comment->author_name ?>
							<?php endif; 
						else: ?>
							<?php echo $comment->author_name ?>
						<?php endif; ?>
						<?php if( $comment->author_url ): ?>
							<a class="comment-author-url" target="_blank" href="<?php echo $comment->author_url ?>"><?php echo $comment->author_url ?></a>
						<?php endif; ?>
					</td>
					<td><?php echo substr(strip_tags($comment->content), 0, 70) . '...'; ?></td>
					<td><?php echo date('c', strtotime($comment->created_at)); ?></td>
					<td>
						<?php if( (int) $comment->comment_parent !== 0 ): ?>
							<a href="<?php echo Url::get('admin@edit_comment', $comment->comment_parent) ?>"><?php echo $comment->comment_parent ?></a>
						<?php endif; ?>
					</td>
					<td><a href="<?php echo Url::get('admin@edit', $comment->post_id); ?>"><?php echo $comment->post_id ?></a></td>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination; ?>
	<?php endif; ?>
</div>