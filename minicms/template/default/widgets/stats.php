<div class="widget widget--stats">
	<h3 class="widget-title">Estadísticas:</h3>
	<div class="widget-content">
		<ul>
			<li>Número total de entradas: <strong><?php echo count($database->posts); ?></strong></li>
			<li>Tiempo de carga hasta aquí en segundos: <strong><?php echo microtime(true) - TIME_START ?></strong></li>
		</ul>
	</div>
</div>