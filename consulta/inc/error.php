<?php
/**
 * Contenido cuando el form se ha enviado y es incorrecto
 * Podríamos dar errores concretos, pero teniendo en cuenta que los errores no pueden ser tantos...
 */
?>
<div class="message message-error">
	<p><strong>Ups!</strong> Parece que los datos que nos has enviado no son adecuados</p>
	<ul>
		<?php

		$field_translations = array(
			'title' => 'Título',
			'text' => 'Consulta',
			'sex' => 'Sexo del paciente',
			'age' => 'Edad del paciente'
		);


		 foreach ($error->get() as $field => $field_errors): ?>
			<li><strong><?php echo isset($field_translations[$field]) ? $field_translations[$field] : ucfirst($field); ?></strong>
				<ul>
					<?php foreach ($field_errors as $field_error): ?>
						<li><?php switch ($field_error) {
								case 'empty':
									echo 'El campo no puede estar vacío';
									break;
								case 'forbidden':
									echo 'Contiene palabras prohibidas';
									break;
								case 'length':
									echo 'Tiene que sobrepasar una longitud mínima';
									break;
								case 'repetitive':
									echo 'Tiene palabras repetidas para rellenar hueco';
									break;
								case 'invalid':
									echo 'El código introducido no es correcto';
									break;
								case 'unknown':
								default:
									echo 'Error desconocido';
									break;
							} ?></li>
					<?php endforeach; ?>
				</ul>

			</li>
		<?php endforeach; ?>
	</ul>
</div>