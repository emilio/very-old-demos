<?php namespace EC\HTTP;
use EC\Event,
	EC\App;
/**
 * Response class
 */
class Response {
	public $response;
	public $type;
	public $http_status;

	public function __construct($type, $response, $status) {
		$this->type = $type;
		$this->response = $response;
		if( is_int($status) ) {
			$this->http_status = $status;
		}
	}

	public function render($echo = false) {
		switch ($this->type) {
			case 'json':
				Header::contentType('application/json');
				$this->response = json_encode($this->response);
			break;
		}

		if( isset($this->http_status) ) {
			Header::status($this->http_status);
		}

		if( $echo ) {
			echo $this->response;
		} else {
			return $this->response;
		}
	}

	public static function json($args, $status = null) {
		return new self('json', $args, $status);
	}

	public static function error($error_code) {
		Header::status($error_code);
		App::$controller = 'error';
		App::$action = $error_code;

		return Event::trigger('error.' . $error_code);
	}
}