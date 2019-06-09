<?php 
	// header('Content-Type: text/plain;charset=UTF-8');
	include 'inc/JK_Counter.php'; 
	include 'inc/JK_4bit_Counter.php';
	include 'inc/HTML_Table.php';
	// Esto es sólo para poder testearlo con el archivo test.php
	if( isset($_POST['nums']) ) {
		$nums = explode(',', $_POST['nums']);
		array_map(function($num) { return (int) $num;}, $nums);
		$counter = new JK_4bit_Counter($nums);
	} else {
		$counter = new JK_4bit_Counter([13, 4, 6, 3, 2, 0, 15,1]);		
	}
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title>Contador aleatorio</title>

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<style>
		html, body {
			font-family: sans-serif;
			font-size: 15px;
			background: #493736;
			color: #333;
			margin: 0;
		}
		img {
			max-width: 100%;
			padding: .5em;
			border: 1px solid #aaa;
			border-radius: 3px;
			box-shadow: 0 0 2px rgba(0,0,0,.4);
			background: #f1f1f1;
			box-sizing: border-box;
		}

		/*.container {
			max-width: 800px;
			margin: 0 auto;
		}*/
		h1 {
			text-align: center;
			color: #fff;
			margin: 0;
			padding: 1em 0;
			/* background: rgba(0,0,0,.2); */
			background: #362928;
		}
		.container > section {
			max-width: 800px;
			margin: 2em auto;
			background: white;
			padding: 0 15px 15px 15px;
			box-shadow: 0 0 5px 3px rgba(0,0,0,0.4);
		}
		.container h2 {
			margin: 0;
			padding: .75em 15px .75em 15px;
			text-align: center;
			margin-bottom: 1em;
			width: 100%;
			margin-left: -15px;
			background: #668284;
			color: white;
		}
		footer {
			background: #7B3B3B;
			padding: 1em 0;
			text-align: center;
			color: white;
			box-shadow: 0 -1px 1px rgba(0,0,0,.4);
		}

		footer a {
			color: #668284;
			text-decoration: none;
		}
		footer a:hover {
			color: #5C7475;
		}
		/**
		 * Estilos para las tablas via purecss.io
		 */
		table{border-collapse:collapse;border-spacing:0;empty-cells:show;border:1px solid #cbcbcb}table caption{color:#000;font:italic 85%/1 arial,sans-serif;padding:1em 0;text-align:center}table td,table th{border-left:1px solid #cbcbcb;border-width:0 0 0 1px;font-size:inherit;margin:0;overflow:visible;padding:6px 12px}table td:first-child,table th:first-child{border-left-width:0}table thead{background:#e0e0e0;color:#000;text-align:left;vertical-align:bottom}table td{background-color:transparent}.pure-table-odd td{background-color:#f2f2f2}.pure-table-striped tr:nth-child(2n-1) td{background-color:#f2f2f2}.pure-table-bordered td{border-bottom:1px solid #cbcbcb}.pure-table-bordered tbody>tr:last-child td,.pure-table-horizontal tbody>tr:last-child td{border-bottom-width:0}.pure-table-horizontal td,.pure-table-horizontal th{border-width:0 0 1px 0;border-bottom:1px solid #cbcbcb}.pure-table-horizontal tbody>tr:last-child td{border-bottom-width:0}

		.table-container--transitions td:nth-child(2n - 1):not(:first-child),
		.table-container--transitions th:nth-child(2n - 1):not(:first-child) {
			border-left-width: 2px;
			border-left-color: #999;
		}
		.table-container--karnaugh td:first-child {
			font-weight: bold;
		}
		table {
			margin: 0 auto;
		}

		td.h1 {
			background: rgba(255, 0, 0, .5);
		}
		td.h2 {
			background: rgba(0, 0, 255, .5);
			color: white;
		}
		td.h1.h2 {
			background: rgba(255,0,255, .5);
		}
		.equation {
			font-family: serif;
			font-style: italic;
		}

		.karnaugh-container {
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
			-webkit-flex-wrap: wrap;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
		}

		.karnaugh-section {
			-webkit-flex: auto;
			-ms-flex: auto;
			flex: auto;
		}
		.karnaugh-section h3 {
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Contador asíncrono</h1>
		<section id="transitions">
			<h2>Tabla de transiciones</h2>
			<div class="table-container table-container--transitions">
				<?php echo HTML_Table::generate($counter->getTransitionData()); ?>
			</div>
		</section>

		<section id="karnaugh">
			<h2>Mapas de Karnaugh</h2>
			<div class="karnaugh-container">
				<?php foreach ($counter->getKarnaughTables() as $table): ?>
					<section id="karnaugh-<?php echo $table['label']; ?>" class="karnaugh-section">
						<h3><?php echo $table['label'] ?></h3>
						<div class="table-container table-container--karnaugh">
							<?php echo HTML_Table::generate($table['table']); ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</section>
		<footer>
			<p>&copy; 2013 Emilio Cobos Álvarez (<a href="http://emiliocobos.net">http://emiliocobos.net</a>) &lt;<a href="mailto:emiliocobos@usal.es">emiliocobos@usal.es</a>&gt;.</p>
			<p>El código está <a href="http://github.com/ecoal95/contador-4-bits">aquí</a></p>
		</footer>
	</div>
	<script src="assets/js/selectable.js"></script>
</body>
</html>