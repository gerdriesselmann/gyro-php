<?php
/**
 * Holds information about a bookmark 
 */
class SocialBookmark {
	public $service = '';
	public $image = '';
	public $url = '';
	
	public function __construct($title, $url, $image) {
		$this->service = $title;
		$this->image = $image;
		$this->url = $url;		
	}
	
	public function get_url($title, $url) {
		$ret = $this->url;
		$ret = str_replace('%URL%', rawurlencode($url), $ret);
		$ret = str_replace('%TITLE%', rawurlencode($title), $ret);
		return $ret;
	}
	
	public function get_image_path() {
		return Config::get_value(Config::URL_BASEDIR) . 'images/bookmarking/' . $this->image;
	}
}