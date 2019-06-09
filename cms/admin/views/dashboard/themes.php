<div class="wrapper">
	<?php if( isset($success_msg) ): ?>
		<div class="message message-success"><?php echo $success_msg ?></div>
	<?php endif; ?>
	<?php if( isset($error_msg) ): ?>
		<div class="message message-error"><?php echo $error ?></div>
	<?php endif; ?>
	<h2 class="section-title">Configuración de temas</h2>
	<div class="themes row">
		<?php foreach ($themes as $theme_alias => $theme):
			$theme_dir = Themes::getThemeDir($theme_alias);
			$theme_dir = Themes::getThemeUrl($theme_alias);
		?>
			<div class="span4">
				<?php if( count($theme->screenshots) ): ?>
					<img class="theme-thumbnail" src="<?php echo $theme_url . $theme->screenshots[0] ?>" alt="<?php echo $theme->name ?>">
				<?php else: ?>
					<img class="theme-thumbnail" src="<?php echo Url::asset('img/default_theme_image.jpg'); ?>" alt="No hay previsualización disponible">
				<?php endif; ?>
				<div class="theme-data">
					<h3 class="theme-name">
						<?php echo $theme->name ?>
							<span class="theme-version text-info"><?php echo $theme->version ?></span>
						<?php if( $theme_alias === $current_theme ): ?>
							<span class="theme-active text-success">activo</span>
						<?php endif; ?>
					</h3>
					<p class="theme-description"><?php echo $theme->description ?></p>
					<p class="theme-author">
						Por 
						<?php if( isset($theme->author->url) && ! empty($theme->author->url) ): ?>
							<a target="_blank" href="<?php echo $theme->author->url ?>"><?php echo $theme->author->name; ?></a>
						<?php else: ?>
							<?php echo $theme->author->name; ?>
						<?php endif; ?>
					</p>
					<form class="theme-actions" method="POST" action="<?php echo Url::get('admin@themes') ?>">
						<input type="hidden" name="theme" value="<?php echo $theme_alias ?>">
						<?php if( $theme_alias === $current_theme ): ?>
							<button disabled title="El tema ya está activo" type="submit" class="btn btn-info pull-right" name="action" value="preview">Previsualizar</button>
							<button disabled title="El tema ya está activo" type="submit" class="btn btn-success " name="action" value="enable">Usar</button>
						<?php else: ?>
							<button type="submit" class="btn btn-info pull-right" name="action" value="preview">Previsualizar</button>
							<button type="submit" class="btn btn-success" name="action" value="enable">Usar</button>
						<?php endif; ?>
					</form>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>