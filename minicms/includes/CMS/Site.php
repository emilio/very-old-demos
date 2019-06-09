<?php namespace CMS;
class Site {
	private $data;
	public function __construct() {
		if( file_exists(PATH . 'site.json') ) {
			$this->data = json_decode(file_get_contents(PATH . 'site.json'));	
		} else {
			throw new \Exception("File site.json not found");
		}
	}
	public function __get($prop) {
		return $this->data->{$prop};
	}
	public function __set($prop, $val) {
		$this->data->{$prop} = $val;
	}
	public function __isset($prop) {
		return isset($this->data->{$prop});
	}
}