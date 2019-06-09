<?php
	/**
	 * Formulario principal
	 */
?>
<?php include 'searchform.php'; ?>
<form action="" method="POST" class="main-form">
	<h2 class="main-form-title">Déjanos tu Consulta Médica</h2>
	<div class="form-field">
		<div class="form-field-description">
			<label for="title">Descripción breve:</label>
			<p>
				Mínimo 20 caracteres<br>
				Ejemplo: Anciana con problemas de Artrosis
			</p>
		</div>
		<div class="form-field-input">
			<input required type="text" class="text-input" name="title" id="title" value="<?php echo @$_POST['title'] ?>">
		</div>
	</div>

	<div class="form-field">
		<div class="form-field-description">
			<label for="sex">Sexo del paciente:</label>
		</div>
		<div class="form-field-input">
			<select id="sex" name="sex">
				<option value="">-- Seleccionar --</option>
				<option value="M"<?php if( @$_POST['sex'] === 'M') echo ' selected';?>>Masculino</option>
				<option value="F"<?php if( @$_POST['sex'] === 'F') echo ' selected'; ?>>Femenino</option>
			</select>
		</div>
	</div>
	<div class="form-field">
		<div class="form-field-description">
			<label for="age">Edad del paciente:</label>
		</div>
		<div class="form-field-input">
			<select id="age" name="age">
				<option value="">-- Seleccionar --</option>
				<?php 
					$age_options = array_merge(array_map(function($i) { return $i . ' meses'; }, range(1, 11)), array_map(function($i) { return $i . ' años'; },range(1, 90))); 
					$current_option = @$_POST['age'];

				foreach ($age_options as $option): ?>
					<option value="<?php echo $option ?>"<?php if($option === $current_option) echo ' selected'; ?>><?php echo $option ?></option>
				<?php endforeach; 
				unset($age_options, $current_option, $option);
				?>
			</select>
		</div>
	</div>
	<div class="form-field">
		<div class="form-field-description">
			<label for="text">Mi consulta es:</label>
			<p>
				Recuerda agregar todos detalles importantes de tu caso, incluyendo todos los síntomas que haya experimentado el paciente, exámenes y procedimientos médicos realizados y los síntomas presentados.
				<br>Mínimo 700 caracteres
			</p>
		</div>
		<div class="form-field-input">
			<textarea required name="text" id="text" class="textarea"><?php echo @$_POST['text'] ?></textarea>
			<p>
				<input type="text" class="text-input" id="text_charcount" readonly value="<?php echo strlen(@$_POST['text']) ?>"> caracteres. Mínimo 700. Mientras más detallado el caso, antes será publicado. (Ideal 3000 caracteres).				
			</p>
		</div>
	</div>
	<?php include BASE . '/inc/ads/insideform.php'; ?>
	<div class="form-field">
		<div class="form-field-description">
			<label for="record">Otros antecedentes:</label>
		</div>
		<div class="form-field-input">
			<textarea name="record" id="record" class="textarea"><?php echo @$_POST['record'] ?></textarea>
		</div>
	</div>
	<div class="recaptcha-field">
    	<?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, isset($recaptcha_error) ? $recaptcha_error : null); ?>		
	</div>
	<div class="form-submit">
		<input type="submit" value="Enviar Consulta Médica" class="btn btn-info btn-big">
	</div>
</form>