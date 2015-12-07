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
 * Build Atom file, targeted by FeedWriter class
 *
 * @author Gerd Riesselmann http://www.gerd-riesselmann.net
 * @ingroup Feeds
 * 
 * Based upon http://www.gerd-riesselmann.net/archives/2005/05/a-braindead-simple-php-feed-writer-class
 * but rewritten to fit Gyro style
 */
class AtomWriter extends FeedWriter {
	/**
	 * Last modificationdate of items
	 * 
	 * @var datetime
	 */
	private $last_mod_date = 0;
	
	/**
	 * Return mime type of feed
	 * 
	 * @return string
	 */
	public function get_mime_type() {
		return 'application/atom+xml';
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
		$tags[] = html::tag_selfclosing('link', array('href' => $item->link));
		$tags[] = html::tag('id', $this->escape($item->guid));
		$tags[] = html::tag('summary', $this->strip_html($item->description));
		
		$updated = $item->last_update ? $item->last_update : $item->pubdate;
		$tags[] = html::tag('updated', GyroDate::iso_date($updated));
		$tags[] = html::tag('published', GyroDate::iso_date($item->pubdate));
		if ($updated > $this->last_mod_date) {
			$this->last_mod_date = $updated;
		}
		
		// Author
		$author_tags = array();
		if ($item->author_name) {
			$author_tags[] = html::tag('name', $this->escape($item->author_name));
		}
		if ($item->author_email) {
			$author_tags[] = html::tag('email', $this->escape($item->author_email));
		}
		if (count($author_tags)) {
			$tags[] = html::tag('author', implode("\n", $author_tags));
		}
		
		// HTML content
		$content = $this->relative_to_absolute($item->content, $item->baseurl);
		$content = GyroString::escape($content, GyroString::XML);
		$tags[] = html::tag('content', $content, array('type' => 'html'));
		
		// Categories
		foreach($item->categories as $cat) {
			$tags[] = html::tag_selfclosing('category', array('scheme' => $cat->domain, 'term' => $cat->title));
		}

		// Enclosures
		foreach($item->enclosures as $enc) {
			$tags[] = html::tag_selfclosing('content', array('type' => $enc->type, 'src' => $enc->url));
		}
		
		return html::tag('entry', implode("\n", $tags));
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
		$tags[] = html::tag('subtitle', $this->strip_html($title->description));
		$tags[] = html::tag('id', $this->escape($title->link));
		$tags[] = html::tag_selfclosing('link', array('rel' => 'alternate', 'type' => 'text/html', 'href' => $title->link));
		// This is recommend
		if ($title->selfurl) {
			$tags[] = html::tag_selfclosing('link', array('href' => $title->selfurl, 'rel' => 'self', 'type' => $this->get_mime_type()));
		}
		
		if ($title->last_updated) {
			$tags[] = html::tag('updated', GyroDate::iso_date($title->last_updated));
		}
		else {
			$tags[] = html::tag('updated', GyroDate::iso_date($this->last_mod_date));
		}
		
		$tags[] = html::tag('generator', $this->escape($title->generator));
		$tags[] = html::tag('rights', $this->escape('Copyright ' . $title->copyright));
		$tags[] = html::tag('author', html::tag('name', $this->escape($title->editor)));
		
		if (!empty($title->imageurl)) {
			$tags[] = html::tag('logo', $this->escape($title->imageurl));
		}
		
		$ret = '<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="' . $this->escape($title->language) . '">';
		$ret .= "\n";
		$ret .= implode("\n", $tags);
		return $ret;
		
	}

	/**
     * Render feed closing
     * 
     * @param string $title
     * @param string $items
     * @return string
	 */
	protected function render_end($title, $items) {
		$ret = '<?xml version="1.0" encoding="' . GyroLocale::get_charset() . '"?>';
		$ret .= "\n";
		$ret .= $title;
		$ret .= "\n";
		$ret .= $items;
		$ret .= "\n";
		$ret .= '</feed>';
		return $ret;
	}
}
