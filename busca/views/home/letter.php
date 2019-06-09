<?php if( 0 === count($terms) ): ?>
	<div class="message message-error">No se ha encontrado ningún resultado</div>
<?php else: ?>
	<ul>
		<?php foreach($terms as $term): ?>
			<li><a href="<?php echo Url::get(null, $term->path); ?>"><?php echo $term->formatted_term ?></a></li>
		<?php endforeach; ?>	
	</ul>
<?php endif; ?>