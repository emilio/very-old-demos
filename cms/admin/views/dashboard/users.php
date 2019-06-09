<div class="users-wrapper">
	<?php foreach ($users as $user): ?>
	<div class="user<?php if(Param::get('created') === $user->id) {echo ' user-created';}; ?>" data-user-id="<?php echo $user->id ?>">
		<a href="<?php echo Url::get('admin@delete_user', $user->id) ?>" title="Eliminar el usuario" id="delete-user-<?php echo $user->id ?>" class="icon-inside user-delete">&times;</a>
		<img src="//gravatar.com/avatar/<?php echo md5(strtolower(trim($user->email))) ?>?s=30&amp;d=mm" alt="" class="user-thumbnail">
		<h5 class="user-name">
			<a href="<?php echo Url::get('authors', $user->username) ?>">
				<?php echo $user->name ?>
				<small class="user-username"><?php echo $user->username ?></small>
			</a>
		</h5>
		<p class="user-stats">
			<a href="<?php echo Url::get('admin@manage', null, 'author=' . $user->username . '&status=publish') ?>">
				<?php echo Post::where('author_id', '=', $user->id)->and_where('status', '=', 'publish')->count(); ?> entradas
			</a>
		</p>
	</div>
	<?php endforeach; ?>
	<div class="users-controls">
		<a href="<?php echo Url::get('admin@add_user') ?>" title="Crear un usuario" id="add-user" class="icon-inside user-add"></a>
	</div>
</div>