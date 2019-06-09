<?php $comment_authors = array(); ?>
<div class="wrapper">
	<form action="<?php echo Url::get('admin@manage'); ?>" class="pull-right" method="GET">
		<label for="q" class="assistive-text">Buscar</label>
		<input type="search" name="q" id="q" class="text-input" placeholder="Introduce tu consulta..." value="<?php echo Param::get('q') ?>">
		<input type="submit" value="Buscar" class="btn btn-info">
	</form>
	<h2 class="section-title">Posts</h2>
	<?php if( Param::get('deleted') ): ?>
		<div class="message message-success">Has borrado el post correctamente</div>
	<?php endif; ?>
	<?php ob_start(); ?>
	<div class="pagination pagination-centered">
		<ul>
		<?php
			$from = 1;
			$to = ceil($total_posts / $per_page);
			for(; $from <= $to; $from++ ): ?>
				<li <?php if( $page === $from ) { echo 'class="active"'; } ?>><a href="<?php echo Url::get('admin@manage', null, array_merge($_GET, array('page' => $from))) ?>"><?php echo $from ?></a></li>
		<?php endfor; ?>
		</ul>
	</div>
	<?php $pagination = ob_get_clean(); ?>
	<?php if( $total_posts === 0 || count($posts) === 0 ): ?>
		<div class="message message-error"><strong>Upps!</strong> No se han encontrado posts relacionados con tu consulta.</div>
	<?php else: ?>
		<?php echo $pagination; ?>
		<table class="table table-stripped table-bordered posts-table main-data-table">
			<thead>
				<tr>
					<th>Id</th>
					<th>Autor</th>
					<th>Título</th>
					<th>Descripción</th>
					<th>Tipo</th>
					<th>Comentarios</th>
					<th title="Si el post está publicado será la fecha de publicación, si no será la de creación">Creado/Publicado</th>
					<th>Estado</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($posts as $post): set_current_post($post);?>
				<tr class="post-table-post" data-post-id="<?php echo $post->id ?>">
					<td><a href="<?php echo Url::get('admin@edit', $post->id) ?>"><?php echo $post->id ?></a></td>
					<td><a title="Ver todos los posts de <?php the_post_author_meta('name') ?>" href="<?php echo Url::get('admin@manage', null, 'author=' . get_post_author_meta('username'));  ?>"><?php the_post_author_meta('name'); ?></a></td>
					<td><?php the_post_title() ?></td>
					<td><?php the_post_description(); ?>...</td>
					<td><?php the_post_type(); ?></td>
					<td><?php echo $post->comment_count; ?></td>
					<td><?php echo date('c', strtotime($post->created_at)); ?></td>
					<td><a href="<?php echo Url::get('admin@manage', null, array_merge($_GET, array('page' => 1,'status' => $post->status))); ?>"><?php echo $post->status ?></a></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination; ?>
	<?php endif; ?>
</div><!-- .wrapper -->