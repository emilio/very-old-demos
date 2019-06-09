<?php
/** 
 * Las tablas de karnaugh las hacemos sólo para 4 bits, haría falta bastante más abstracción y tiempo para hacerlo para cualquier número de bits
 */
class JK_4bit_Counter extends JK_Counter {
	public $karnaughTables;
	public function __construct($values) {
		parent::__construct(4, $values);
	}
	private function _getTransitionDataFromState($state) {
		foreach ($this->getTransitionData() as $transitionState) {
			if( $transitionState['from'] === $state ) {
				return $transitionState;
			}
		}
		return null;
	}
	public function getKarnaughTables($refresh = false) {
		if( ! isset($this->karnaughTables) || $refresh) {
			$bitcols = ['00','01', '11', '10'];
			$transitionData = $this->getTransitionData();
			$this->karnaughTables = [];
			$currentTransitionData = null;

			for( $i = 0; $i < $this->bitCount; $i++ ) {
				$jtable = [];
				$ktable = [];
				
				for( $j = 0; $j < $this->bitCount; $j++ ) {
					$jrow = [];
					$krow = [];


					$krow[''] = $jrow[''] = $bitcols[$j];
					for($k = 0; $k < $this->bitCount; $k++) {
						$currentTransitionData = $this->_getTransitionDataFromState($bitcols[$k] . $bitcols[$j]);
						$jrow[$bitcols[$k]] = $currentTransitionData['J' . $i];
						$krow[$bitcols[$k]] = $currentTransitionData['K' . $i];
					}
					$jtable[] = $jrow;
					$ktable[] = $krow;
				}

				$this->karnaughTables[] = [
					'label' => 'J' . $i,
					'table' => $jtable
				];
				$this->karnaughTables[] = [
					'label' => 'K' . $i,
					'table' => $ktable
				];
			}
		}
		return $this->karnaughTables;
	}
}