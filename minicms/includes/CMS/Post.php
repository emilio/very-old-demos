<?php namespace CMS;
class Post {
	private $data;
	public function __construct($slug) {
		$this->data = json_decode(file_get_contents(POSTS_PATH . $slug . '.json'));
		$this->data->slug = $slug;
	}
	public function __get($prop) {
		return $this->data->{$prop};
	}
	public function __isset($prop) {
		return isset($this->data->{$prop});
	}
}