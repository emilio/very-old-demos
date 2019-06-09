<?php namespace CMS;
class Route {
	/** The regular expresion */
	private $expr;
	/** The callback function */
	private $callback;
	/** The matches of $expr */
	private $matches;

	/**
	 * Constructor
	 */
	public function __construct($expr, $callback) {
		$this->expr = '#^' . $expr . '$#';
		$this->callback = $callback;
	}

	/**
	 * See if route matches with path
	 */
	public function matches($path) {
		if( preg_match($this->expr, $path, $this->matches) ) {
			return true;
		}
		return false;
	}

	/**
	 * Exec the callback. The matches function needs to be called before this and return true
	 */
	public function exec() {
		return call_user_func_array($this->callback, array_slice($this->matches, 1));
	}
}