	</div><!-- /.container -->
	<?php include 'inc/ads/bottom.php'; ?>
	<footer class="page-footer" role="contentinfo">

		<p class="tos-info">
			<?php if( isset($_GET['show']) && $_GET['show'] === 'tos' ): ?>
				<a href="?">Volver al formulario</a>
			<?php else: ?>
				Al enviar este formulario aceptas haber leído y aceptado los <a title="Términos y condiciones del Tablón gratis de consultas médicas online gratis" href="?show=tos">términos y condiciones</a>
			<?php endif; ?>
		</p>

		<p class="comeback">
			<a href="http://inmedicina.org/" title="InMedicina: El sitio más completo sobre información médica y cuidado de la salud">Volver a InMedicina.org</a> - <a href="http://inmedicina.org/categoria/consultas-medicas" title="Ver consultas enviadas al de consultas médicas online gratis" target="_blank">Ver consultas médicas enviadas</a>				
		</p>
	</footer>

	<script src="js/script.js"></script>
</body>
</html>