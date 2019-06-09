<?php foreach ($posts as $post): ?>
	<?php include 'post-index.php'; ?>
<?php endforeach; ?>
<?php if( $page->paged > 0 ): ?>
	<p>Concretamente la página <?php echo $page->paged ?></p>
<?php endif; ?>