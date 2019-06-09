<?php if( file_exists(BASE_PATH . 'install.php') ): ?>
	<div class="row">
		<div class="message message-error">Recuerda eliminar el archivo <code>install.php</code>, para evitar posibles riesgos (desde allí se puede cambiar la descripción y demás).</div>
	</div>
<?php endif; ?>
<div class="modules-wrapper row">
	<div class="module posts-stats-module span6">
		<h2 class="module-title">Estadísticas</h2>
		<div class="module-content">
			<p>
				<span class="badge badge-success"><?php echo get_posts()->where('status', '=', 'publish')->count(); ?></span> <a href="<?php echo Url::get('admin@manage', null, 'status=publish');?>">Entradas publicadas</a>
			</p>
			<p>
				<span class="badge badge-info"><?php echo get_posts()->where('status', '=', 'draft')->count(); ?></span> <a href="<?php echo Url::get('admin@manage', null, 'status=draft') ?>">Borradores</a>
			</p>
		</div>
	</div>
	<div class="module comments-module span6">
		<h2 class="module-title">Comentarios</h2>
		<div class="module-content">
			<?php 
				$total_comments = Comment::count();
				$approved_comments = Comment::where('approved', '=', 1)->count();
				$moderated_comments = $total_comments - $approved_comments;
				?>
			<p>
				<strong><?php echo $total_comments ?></strong> comentarios
			</p>
			<p>
				<span class="badge badge-success"><?php echo $approved_comments ?></span> <a href="<?php echo Url::get('admin@manage_comments', null, 'approved=1');?>">Comentarios aprobados</a>
			</p>
			<p>
				<span class="badge badge-info"><?php echo $moderated_comments ?></span> <a href="<?php echo Url::get('admin@manage_comments', null, 'approved=0') ?>">Comentarios a la espera de moderación</a>
			</p>
		</div>
	</div>
</div>