<?php
/**
 * Class to format item in sitemap
 */
class GSiteMapItemFormatter {
	protected $url = '';
	protected $data = array();


	/**
	 * @param string $url
	 * @param array $data
	 */
	public function __construct($url, $data) {
		$this->url = $url;
		$this->data = $data;
	}

	/**
	 * Render as XML
	 *
	 * @return string
	 */
	public function as_xml() {
		$tags = $this->get_xml_tags();
		return '<url>' . $this->xml_from_tags($tags) . '</url>';
	}

	/**
	 * Collect tags to output
	 * @return array
	 */
	protected function get_xml_tags() {
		$tags = array();
		$tags['loc'] = $this->url;
		if (Arr::get_item($this->data, 'lastmod', 0) > 0) { $tags['lastmod'] = GyroDate::iso_date($this->data['lastmod']); }
		if (!empty($this->data['priority'])) { $tags['priority'] = $this->data['priority']; }
		if (!empty($this->data['changefreq'])) { $tags['changefreq'] = $this->data['changefreq']; }

		return $tags;
	}

	/**
	 * Render as HTML
	 *
	 * @return string
	 */
	public function as_html() {
		$s_url = GyroString::escape($this->url);
		return "<a href=\"$s_url\">$s_url</a>";
	}

	/**
	 * Turn associative array of tag => content into XML
	 *
	 * @param array $tags
	 * @return string
	 */
	protected function xml_from_tags($tags) {
		$ret = '';
		foreach($tags as $tag => $content) {
			$c = is_array($content) ? $this->xml_from_tags($content) : GyroString::escape($content, GyroString::XML);
			$ret .= "<$tag>" . $c . "</$tag>\n";
		}
		return $ret;
	}
}
