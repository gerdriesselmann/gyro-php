<?php
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

require_once dirname(__FILE__) . "/feedwriter/feedwriter.cls.php";

/**
 * Build RSS file, targeted by FeedWriter class
 *
 * @author Gerd Riesselmann http://www.gerd-riesselmann.net
 */
class RSSWriter extends FeedWriter {	
	/**
	 * Return mime type of feed
	 * 
	 * @return string
	 */
	public function get_mime_type() {
		return 'application/rss+xml';
	}

	/**
	 * Render an item
	 * 
	 * @param FeedWriterItem $item
	 * @return string
	 */
	protected function render_item(FeedWriterItem $item) {
		$tags = array();
		
		// We misuse html class to generate tags :)
		$tags[] = html::tag('title', $this->escape($item->title));
		$tags[] = html::tag('link', $this->escape($item->link));
		$tags[] = html::tag('description', $this->strip_html($item->description));
		$tags[] = html::tag('pubDate', GyroDate::http_date($item->pubdate));
		$tags[] = html::tag('guid', $this->escape($item->guid), array('isPermaLink' => 'true'));
		if (!empty($item->author_email)) {
			$tags[] = html::tag('author', $this->escape($item->author_email));
		}
		if (!empty($item->author_name)) {
			$tags[] = html::tag('dc:creator', $this->escape($item->author_name));
		}
		
		// HTML content
		$content = $this->relative_to_absolute($item->content, $item->baseurl);
		$content = '<![CDATA[' . $content . ']]>';
		$tags[] = html::tag('content:encoded', $content);
		
		// Categories
		foreach($item->categories as $cat) {
			$tags[] = html::tag('category', $this->escape($cat->title), array('domain' => $cat->domain));
		}

		// Enclosures
		foreach($item->enclosures as $enc) {
			$tags[] = html::tag_selfclosing('enclosure', array(
				'url' => $enc->url,
				'length' => $enc->length,
				'type' => $enc->type
			));
		}
		
		return html::tag('item', implode("\n", $tags));
	}

	/**
     * Render feed title section
     * 
     * @param FeedWriterTitle $title
     * @return string
	 */
	protected function render_title(FeedWriterTitle $title) {
		// We misuse html class to generate tags :)
		$tags = array();
		$tags[] = html::tag('title', $this->escape($title->title));
		$tags[] = html::tag('link', $this->escape($title->link));
		$tags[] = html::tag('description', $this->strip_html($title->description));
		if ($title->last_updated) {
			$tags[] = html::tag('pubDate', GyroDate::http_date($title->last_updated));
		}
		$tags[] = html::tag('language', $this->escape($title->language));
		$tags[] = html::tag('generator', $this->escape($title->generator));
		$tags[] = html::tag('copyright', $this->escape($title->copyright));
		$tags[] = html::tag('managingEditor', $this->escape($title->editor));
		
		if (!empty($title->imageurl)) {
			$image = '';
			$image .= html::tag('title', $this->escape($title->title));
			$image .= html::tag('link', $this->escape($title->link));
			$image .= html::tag('url', $this->escape($title->imageurl));
			if (!empty($title->image_width) && !empty($title->image_height)) {
				$image .= html::tag('width', Cast::int($title->image_width));
				$image .= html::tag('height', Cast::int($title->image_height));
			}			
			
			$tags[] = html::tag('image', $image);
		}

		// This is recommend
		if ($title->selfurl) {
			$tags[] = html::tag_selfclosing(
				'atom:link',
				array(
					'href' => $title->selfurl,
					'rel' => 'self',
					'type' => $this->get_mime_type()
				)
			);
		}
		
		$ret = implode("\n", $tags);
		return $ret;
	}

	/**
     * Render feed closing
     * 
     * @param string $title
     * @param string $items
     * @return string
	 */
	function render_end($title, $items) {
		$channel = html::tag('channel', $title . $items);
		$rss = html::tag(
			'rss',
			$channel,
			array(
				'version' => '2.0', 
				'xmlns:content' => 'http://purl.org/rss/1.0/modules/content/',
				'xmlns:wfw' => 'http://wellformedweb.org/CommentAPI/',
				'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
				'xmlns:atom' => 'http://www.w3.org/2005/Atom'
			)
		);
		
		$ret = '<?xml version="1.0" encoding="' . GyroLocale::get_charset() . '"?>' . "\n" . $rss;
		return $ret;
	}
}
