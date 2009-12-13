<?php
/**
 * Feed Writer base class
 *
 * @author Gerd Riesselmann http://www.gerd-riesselmann.net
 */

/*
Copyright (C) 2005 Gerd Riesselmann

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

http://www.gnu.org/licenses/gpl.html
*/


/**
 * Simple Date Structure holding feed data
 */
class FeedWriterTitle {
	public $title = '';
	public $description = '';
	public $link = '';

	public $last_updated = 0;

	public $copyright = ''; 
	public $editor = '';
	
	public $imageurl = '';
	public $image_width = '';
	public $image_height = '';
	
	public $generator = '';
	public $language = "en";
	public $selfurl = '';
	
	public function __construct() {
		$this->title = Config::get_value(Config::TITLE);
		$this->link = Config::get_url(Config::URL_BASEURL);
		$this->language = GyroLocale::get_language();
		$this->selfurl = Url::current()->build();
	}
}

/**
 * Simple Date Structure holding feed item data
 */
class FeedWriterItem {
	public $description = '';
	public $title = '';
	public $link = '';
	public $pubdate = 0;
	public $last_update = 0;
	public $guid = '';
	public $author_name = '';
	public $author_email = '';
	public $content = '';
	public $categories = array();
	public $enclosures = array();
	
	public $baseurl = '';
}

/**
 *  Data Structure holding a category
 */
class FeedWriterCategory {
	public $domain = '';
	public $title = '';
	
	public function __construct($title = '', $domain = '') {
		$this->domain = $domain;
		$this->title = $title;
	}
}

/**
 * Date Structure for enclosures
 */
class FeedWriterEnclosures {
	public $url = '';
	public $length = 0;
	public $type = '';
}

class FeedWriter implements IRenderer {
	protected $items;
	/**
	 * @var FeedWriterTitle
	 */
	protected $title;
	
	/**
     * Constructor
     * 
     * @param FeedWriterTitle $title
     * @param array $items
	 */
	public function __construct(FeedWriterTitle $title, $items) {
		$this->items = $items;
		$this->title = $title;
	}
	
	/**
	 * Return mime type of feed
	 * 
	 * @return string
	 */
	public function get_mime_type() {
		return 'application/xml';
	}
	
	/**
	 * Renders what should be rendered
	 *
	 * @param int $policy Defines how to render, meaning depends on implementation
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$title = $this->render_title($this->title);

		$items = '';
		foreach($this->items as $item) {
			$items .= $this->render_item($item);
		}
		
		$ret = $this->render_end($title, $items);		
		return $ret;
	}

	/**
     * Escape properties
	 */
	protected function escape($obj) {
		return String::escape($obj, String::XML);
	}

	/**
	 * Render an item
	 * 
	 * @param FeedWriterItem $item
	 * @return string
	 */
	protected function render_item(FeedWriterItem $item) {
		return '';
	}

	/**
     * Render feed title section
     * 
     * @param FeedWriterTitle $title
     * @return string
	 */
	protected function render_title(FeedWriterTitle $title) {
		return '';
	}

	/**
     * Render feed closing
     * 
     * @param string $title
     * @param string $items
     * @return string
	 */
	protected function render_end($title, $items) {
		return '';
	}
	
	/**
	 * Strip off html from 
	 */
	protected function strip_html($text) {
		return 	String::clear_html(String::unescape(str_replace("&nbsp;", '', $text)));
	}

	/**
	 * Helper. Turn relative URLs in text to an absolute one
	 * 
	 * @param string $text Text containing URLs
	 * @param string $base baseurl
	 */
	protected function relative_to_absolute($text, $base) {
		if (empty($base))
			return $text;
			
		if (substr($base, -1, 1) != "/")
			$base .= "/";
		
		$pattern = 	"/<a([^>]*) href=\"[^http|ftp|https]([^\"]*)\"/";
		$replace = "<a\${1} href=\"" . $base . "\${2}\"";
		$text = preg_replace($pattern, $replace, $text);
		
		$pattern = 	"/<img([^>]*) src=\"[^http|ftp|https]([^\"]*)\"/";
		$replace = "<img\${1} src=\"" . $base . "\${2}\"";
		$text = preg_replace($pattern, $replace, $text);
		
		return $text;
	}
}
