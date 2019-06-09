<?php

/**
 * Normalmente documento las clases en inglés, pero haremos una excepción...
 */
class JK_Counter {
	public $states;
	public $transitionData;

	/**
	 * Formatea un número de tal manera que lo convierte a un binario de tanta longitud como los bits del contador
	 * Por ejemplo: si pasamos 2 en un contador de 4 bits => "0010" 
	 * @param int $num
	 * @return string
	 */
	private function _formatDecimalToBinary($num) {
		return str_pad(decbin($num), $this->bitCount, "0", STR_PAD_LEFT);
	}

	/**
	 * Esta función es equivalente a comparar la tabla de transiciones del biestable JK
	 * @param int $prev valor anterior de la q del biestable
	 * @param int $next valor siguiente
	 * @return array
	 */
	private static function getJKEntries($prev, $next) {
		if( $next === 'x' ) {
			return [
				'j' => 'x',
				'k' => 'x'
			];
		}
		if( $prev === '0' ) {
			if( $next === '1' ) {
				return [
					'j' => '1',
					'k' => 'x'
				];
			}

			return [
				'j' =>'0',
				'k' => 'x'
			];
		}
		if( $next === '0' ) {
			return [
				'j' => 'x',
				'k' => '1'
			];
		}
		return [
			'j' => 'x',
			'k' => '0'
		];
	}

	/**
	 * @constructor
	 * @param int $bitCount el número de bits que tendrán los números de nuestro contador
	 * @param array $values los valores que tendrá el contador. Esta clase no soporta contadores con valores repetidos, cada valor irá a parar a un sólo valor
	 */
	public function __construct($bitCount, array $values) {
		/** Los diferentes estados del contador */
		$this->states = []; // Prefiero la sintaxis [] frente a la de array(), a pesar de que es PHP 5.4+

		/** Almacenamos en la instancia el número de bits */
		$this->bitCount = $bitCount;

		/** El número de valores que tomará el contador */
		$valueCount = count($values);

		/** 
		 * El número máximo que podremos llegar a contar (2^bitCount).
		 * En realidad es uno más, p.ej. si usamos cuatro bits $maxnum vale 16 aunque el máximo número es 15,
		 * pero como sólo vamos a hacer un for nos facilita la tarea
		 */
		$maxNum = pow(2, $bitCount);

		/** Index del siguiente item del contador, ver el loop inferior */
		$nextItemIndex = null;

		/** Valor al que sigue el elemento actual, ver el loop */
		$to = null;

		/** cualquier valor va a parar a xxxx en principio */
		$default_to = str_repeat('x', $bitCount);

		// Llevaba meses sin usar un for en php... con lo cómodo que es el foreach
		for( $i = 0; $i < $maxNum; $i++ ) {
			$to = $default_to;

			// Si el contador tiene que tomar ese valor
			if( ($nextItemIndex = array_search($i, $values)) !== false ) {
				$nextItemIndex++;
				if( $nextItemIndex === $valueCount ) {
					$nextItemIndex = 0;
				}

				// Apuntamos al número en binario
				$to = $this->_formatDecimalToBinary($values[$nextItemIndex]);
			}

			$this->states[$this->_formatDecimalToBinary($i)] = $to;
		}
	}

	public function getTransitionData($refresh = false) {
		if( ! isset($this->transitionData) || $refresh ) {
			$this->transitionData = [];
			$row = null;
			$latchentries = null;
			$i = null;
			$post = null;
			$bitCount = $this->bitCount;
			foreach ($this->states as $from => $to) {
				$from = (string) $from;
				$i = $bitCount;
				$row = [
					'from' => $from,
					'to' => $to
				];

				// Así hacemos el loop de tal manera que las últimas columnas sean J0, K0
				while($i--) {
					$pos = $bitCount - $i - 1;
					$latchentries = static::getJKEntries($from[$pos], $to[$pos]);
					$row['J' . $i] = $latchentries['j'];
					$row['K' . $i] = $latchentries['k'];
				}
				$this->transitionData[] = $row;
			}
		}
		return $this->transitionData;
	}
}