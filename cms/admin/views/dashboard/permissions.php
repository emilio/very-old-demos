<div class="wrapper">
	<h2 class="section-title">Permisos de archivos/carpetas</h2>
	<table>
		<thead>
			<th>Archivo</th>
			<th>Permiso actualmente</th>
		</thead>
		<tbody>
			<?php foreach ($files as $file): ?>
			<tr>
				<td><?php echo $file ?></td>
				<?php if(is_writable($file)): ?>
					<td class="text-success">Sí</td>
				<?php else: ?>
					<td class="text-error">No</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<h2 class="section-title">Consejos importantes:</h2>
	<ul>
	<li>
		<p>Para dar permisos a un archivo o carpeta, puedes seguir los siguientes pasos:</p>
		<ol>
			<li>Si usas CPanel: Administrador de archivos &rsaquo; Click derecho sobre el archivo/carpeta &rsaquo; Cambiar permisos</li>
			<li>Desde Filezilla: Sobre el archivo/carpeta &rsquo; Click derecho &rsquo; Permisos de archivo</li>
		</ol>
	</li>
	<li><p>Los permisos más adecuados son <strong>755</strong>, pero en muchos casos no son suficientes y harán falta <strong>777</strong>. Se recomienda probar primero con 755, volver a esta página, y si es necesario cambiarlos.</p></li>
	<li><p>Si es una carpeta con contenido dentro, se recomienda dar los permisos a todos los subdirectorios/subarchivos.</p></li>
</div>

