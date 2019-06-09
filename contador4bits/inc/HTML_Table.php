<?php

/**
 * Clase cutre para generar tablas a partir de una array
 */
class HTML_Table {
	public static function generate(array $array) {
		$keys = array_keys($array[0]);
		$values = count($array);
		ob_start();
		?>
		<table>
			<thead>
				<?php foreach ($keys as $key): ?>
					<th><?php echo htmlspecialchars($key); ?></th>
				<?php endforeach; ?>
			</thead>
			<tbody>
				<?php foreach ($array as $item): ?>
					<tr>
						<?php foreach($keys as $key): ?>
							<td class="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars(@$item[$key]); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		$table = ob_get_clean();
		return $table;
	}
}