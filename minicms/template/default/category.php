<h2>Aquí iría el contenido de la categoría <?php echo $page->category_name ?></h2>

<?php foreach ($posts as $post): ?>
	<?php include 'post-index.php'; ?>
<?php endforeach; ?>