<?php
/**
 * @deprecated USe constant EMTPY_ATTRIBUTE 
 */
define ('HTML_ATTR_EMPTY', '[[[[[empty]]]]');

/**
 * Class that wrappes common HTML
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class html
{
	/**
	 * Indicates an empty attribute
	 * @var string
	 */
	const EMPTY_ATTRIBUTE = '[[[[[empty]]]]';
	
	/**
	 * Static. Returns the code for a link
	 *
	 * Returns the follwoing: <a [class="$cls"] href="$href" $title="$descr">$text</a>
	 *
	 * @param String The Text for the link
	 * @param String The HREF
	 * @param String A Desctiption, displayed as tool tip
	 * @param String Other atrributes
	 * @returns String The code for an anchor tag
	 */
	public static function a($text, $href, $descr, $attrs = array()) {
		// We need path here, but cannot rely on URL class, since href may be
		// horribly broken 
		$url = str_replace(Config::get_url(Config::URL_SERVER), "", $href);
		$url = str_replace(Config::get_url(Config::URL_SERVER_SAFE), "", $href);
		$bIsActive = Url::current()->is_ancestor_of($url);
	  		
	  	if ($bIsActive) {
	  		html::_appendClass($attrs, 'active');
		}
		
		$attrs['href'] = $href;
		if ($descr) {
			$attrs['title'] = $descr;
		}

		return html::tag('a', $text, $attrs);
	}

	/**
	 * Static. Returns code for title tag and related meta tags
	 *
	 * @param String The title of the page
	 * @param String Optional. A wider description of the page
	 * @returns String The HTML code for title and meta tags
	 */
	public static function title($text, $descr = "") {
		$arrFilter = array("-", ",", ".", "(", ")", ":", "'", '"', "&");
		$keywords = str_replace($arrFilter, " ",  $descr);
		$keywords = str_replace("  ", " ", $keywords);
		$keywords = str_replace(" ", ",", $keywords);

		$ret = self::tag('title', String::escape($text)) . "\n";
		$ret .= self::meta('title', $text) . "\n";
		$ret .= self::meta('description', $descr) . "\n";
		$ret .= self::meta('keywords', $keywords) . "\n";
		return $ret;
	}

	public static function img($path, $alt, $attrs = array()) {
		if ($alt === '') {
			$alt = self::EMPTY_ATTRIBUTE;
		}
		$attrs['src'] = $path;
		$attrs['alt'] = $alt;
		return self::tag_selfclosing('img', $attrs);
	}

	/**
	 * Static. Returns $text formatted error.
	 *
	 * @param String The Error Message
	 * @returns String The HTML Code for outputting an error
	 */
	public static function error($text) {
		return html::p($text, "error");
	}

	public static function success($text) {
		return html::p($text, "success");
	}

	public static function note($text) {
		return html::p($text, "note");
	}

	public static function warning($text) {
		return html::p($text, "warning");
	}
	
	public static function info($text) {
		return html::p($text, "info");
	}

	public static function div($text, $cls = '') {
		return html::tag('div', $text, array('class' => $cls));
	}

	public static function p($text, $cls = '') {
		return html::tag('p', $text, array('class' => $cls));
	}

	public static function span($text, $cls = '') {
		return html::tag('span', $text, array('class' => $cls));
	}


	public static function b($text, $cls = "") {
		return html::tag('strong', $text, array('class' => $cls));
	}

	public static function em($text, $cls = "") {
		return html::tag('em', $text, array('class' => $cls));
	}
	
	public static function h($text, $level, $cls="") 	{
		return html::tag('h' . $level, $text, array('class' => $cls));
	}

	/**
	 * returns a tag surrounding text
	 */
	public static function tag($tag, $text, $attrs = array()) {
		return '<' . $tag . html::attrs($attrs) . ">" . $text . "</" . $tag . ">";
	}

	/**
	 * Returns a self closing tag
	 */
	public static function tag_selfclosing($tag, $attrs = array()) {
		return '<' . $tag . html::attrs($attrs) . " />";
	}


	/**
	 * Returns HTMl code for a list
	 *
	 * @param Array Array of list items
	 * @param String Possible class name
	 * @param Boolean True if list shoudl be ordered
	 * @return String
	 */
	public static function li($items, $cls = '', $useOrdered = false) {
		$c = count($items);
		if ($c == 0) {
			return ''; 
		}
		
		$li = '';
		$i = 0;
		foreach($items as $item) {
			$arr_cls = array($cls);
			$arr_cls[] = (++$i % 2) ? 'uneven' : 'even';
			if ($i === 1) {
				$arr_cls[] = 'first';
			}
			if ($i === $c) {
				$arr_cls[] = 'last';
			}
			$li .= html::tag('li', $item, array('class' => $arr_cls));
		}

		$tag = ($useOrdered) ? 'ol' : 'ul';
		return html::tag($tag, $li, array('class' => $cls));
	}

	/**
	 * Create an submit button
	 */
	public static function submit($text, $name, $descr, $attrs = array()) {
		$attrs['alt'] = $descr;
		$attrs['title'] = $descr;
		$attrs['value'] = $text;
		return self::input('submit', $name, $attrs);
	}
	
	public static function label($title, $for, $cls = '') {
		$attrs = array(
			'for' => $for,
			'class' => $cls
		);
		return self::tag('label', self::span($title), $attrs);
	}

	/**
	 * Returns an input element
	 */
	public static function input($type, $name, $attrs) {
		$attrs['name'] = $name;
		$attrs['type'] = $type;
		self::_appendClass($attrs, $type);
		return self::tag_selfclosing('input', $attrs);		
	}
	
	/**
	 * Return a form 
	 */
	public static function form($name, $action, $content, $method = 'post', $attrs = array()) {
		$attrs['name'] = $name;
		$attrs['id'] = Arr::get_item($attrs, 'id', $name);
		$attrs['action'] = $action;
		$attrs['method'] = $method;
		return self::tag('form', $content, $attrs);
	} 

	/**
	 * Builds select box
	 *
	 * @param string $name
	 * @param array $options
	 * @param string $value
	 * @param array $attrs
	 * @return string
	 */
	public static function select($name, $options, $value, $attrs = array()) {
		$opts = self::options(Arr::force($options), Arr::force($value, false));
		$attrs['name'] = $name;
		return html::tag('select', $opts, $attrs);
	}

	/**
	 * Build all options
	 *
	 * @param array $options
	 * @param array $selected_values Selectd values
	 * @return unknown
	 */
	private static function options($options, $selected_values) {
		$opts = '';
		foreach($options as $option => $display) {
			$opts .= "\n" . self::option($option, $display, $selected_values); 
		}
		return $opts;
	}
	
	/**
	 * Builds option element
	 *
	 * @param string $key
	 * @param string $display
	 * @param array $selected_values
	 * @return string
	 */
	private static function option($key, $display, $selected_values) {
		$ret = '';
		if (is_array($display)) {
			$opts = self::options($display, $selected_values);
			$ret = html::tag('optgroup', $opts, array('label' => $key));
		}
		else {
			$opt_attrs = array();
			if (empty($key)) {
				$key = self::EMPTY_ATTRIBUTE; 
			}
			elseif (in_array($key, $selected_values)) {
				$opt_attrs['selected'] = 'selected';
			}
			$opt_attrs['value'] = $key;
			$ret = html::tag('option', String::escape($display), $opt_attrs);
		} 		
		return $ret;
	}
	 
	/**
	 * Returns code for attribute, including leading blank.
	 *
	 * @return string Empty string if passed value is empty
	 * @exception 
	 */
	public static function attr($name, $value) {
		$value = str_replace("\n", ' ', $value);
		if ($value === self::EMPTY_ATTRIBUTE) {
			$value = '';
		} else if (empty($value) && strval($value) !== '0') {
			return '';
		}

		// remove every non-word character
		$clean_name = preg_replace('|[^\w:_-]|', '', $name);
		if ($clean_name === '') {
			return '';
		} 

		return ' ' . $clean_name . '="' . String::escape($value) . '"';
	}

	public static function attrs($arr) {
		$ret = '';
		foreach($arr as $key => $value) {
			if ($key === 'class' && is_array($value)) {
				$value = implode(' ', $value);
			}
			$ret .= html::attr($key, $value);
		}
		return $ret;
	}

	public static function include_js($scriptName) {
		return '<script type="text/javascript" src="' . String::clear_html($scriptName) . '"></script>';
	}

	/**
	 * Create script tag with content
	 *
	 * @param string $content The script
	 * @return string
	 */
	public static function script_js($content) {
		$content = "<!--// <![CDATA[\n" . $content . "\n// ]]> -->";
		$attrs = array(
			'type' => 'text/javascript'
		);
		return html::tag('script', $content, $attrs);  
	}
	
	public static function include_css($cssName, $media = 'screen') {
		return '<style type="text/css" media="' . String::clear_html($media) . '">@import url(' . String::clear_html($cssName) . ');</style>';
	}

	/**
	 * Creates META tag
	 *
	 * @param string $name
	 * @param string $content
	 * @return string
	 */
	public static function meta($name, $content) {
		$attrs = array(
			'name' => $name,
			'content' => $content
		);
		return self::tag_selfclosing('meta', $attrs);
	}
	
	/**
	 * Creates META http-equiv tag
	 *
	 * @param string $equiv
	 * @param string $content
	 * @return string
	 */
	public static function meta_http_equiv($equiv, $content) {
		$attrs = array(
			'http-equiv' => $equiv,
			'content' => $content
		);
		return self::tag_selfclosing('meta', $attrs);
	}
	
	/**
	 * returns a br-tag
	 */
	public static function br($cls = ''){
		return '<br' . html::attr('class', $cls) . ' />';
	}
	
	/**
	 * Returns html to embed flash files
	 * 
	 * @param string $file URL of flash file
	 * @param array $attrs HTML attributes fo robject tag
	 * @param array $params Flahs params (rendered as param tags)  
	 */
	public static function flash($file, $attrs = array(), $params = array()) {
		// See http://latrine.dgx.cz/how-to-correctly-insert-a-flash-into-xhtml
		$alternative = tr('You need flash to be installed, to view this content', 'core');
		if (!isset($params['wmode'])) {
			$params['wmode'] = 'transparent';
		}
		$param_tags = '';
		foreach($params as $key => $value) {
			$param_tags .= self::tag_selfclosing('param', array('name' => $key, 'value' => $value)) . "\n";
		}
		
		$ie_attrs = array_merge($attrs, array(
			'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000', 
			'codebase' => 'http://get.adobe.com/shockwave/'
		));
		$other_attrs = array_merge($attrs, array(
			'type' => 'application/x-shockwave-flash', 
			'data' => $file,
		));
		
		$ret = '';
		// Non-IE version
		$ret .= "<!--[if !IE]> -->\n" . html::tag('object', $param_tags, $other_attrs) . "\n<!-- <![endif]-->\n";
		// IE Version
		$param_tags .= self::tag_selfclosing('param', array('name' => 'movie', 'value' => $file)) . "\n";
		$ret .= "<!--[if IE]> \n" . html::tag('object', $param_tags, $ie_attrs) . "\n<![endif]-->\n";

		return $ret;
	}

	/**
	 * Build a table cell. 
	 */
	public static function td($text, $attr = array(), $is_head = false) {
		if ($is_head) {
			return self::tag('th', $text, $attr);
		}
		return self::tag('td', $text, $attr);
	}

	/**
	 * Combine table cells into a row.
	 * 
	 * Table cells must be enclosed in <td> or <th> already
	 */
	public static function tr($cells, $attr = array()) {
		$text = implode("\n", Arr::force($cells));
		return self::tag('tr', $text, $attr);
	}
	
	/**
	 * Output a table
	 * 
	 * The table rows are labeled with classes "first", "last" and "even"/"uneven"
	 * 
	 * If you pass an array as $rows or $head, instead an array of arrays, it is
	 * treated as one single row.
	 * 
	 * @since 0.5.1
	 * 
	 * @param array $rows Array of arrays of body cells. Cells must be already formated with either <td> or <th>
	 * @param array $head Array of arrays of head cells. Cells must be already formated with either <td> or <th>
	 * @param string $summary Table summary
	 * @param array $attr Additional html attributes
	 * 
	 * @return string
	 */
	public static function table($rows, $head, $summary, $attr = array()) {
		$ret = '';
		
		$body = self::table_build_rows($rows);
		$head = self::table_build_rows($head);
		
		$content = '';
		$content .= ($head) ? html::tag('thead', $head) . "\n" : '';
		$content .= ($body) ? html::tag('tbody', $body) . "\n" : '';
		
		$attr['summary'] = $summary;
		$ret .= html::tag(
			'table', 
			$content, 
			$attr
		);
		
		return $ret;
	}
	
	/**
	 * Build rows
	 * 
	 * @param array  $rows Array or Array of arrays of cells. Cells must be already formated with either <td> or <th>
	 */
	private function table_build_rows($rows) {
		$ret = '';
		$i = 0;		
		$c = count($rows);
		// Test if $rwos is array or array of arrays;
		if ($c && !is_array($rows[0])) {
			$rows = array($rows);
		}
		// Iterate and output
		foreach($rows as $cells) {
			$arr_cls = array();
			$arr_cls[] = (++$i % 2) ? 'uneven' : 'even';
			if ($i === 1) {
				$arr_cls[] = 'first';
			}
			if ($i === $c) {
				$arr_cls[] = 'last';
			}
			$ret .= html::tr($cells, array('class' => $arr_cls));
		}
		return $ret;
	}
	
	private static function _appendClass(&$attrs, $cls) {
		$oldcls = Arr::get_item($attrs, 'class', '');
		if (!empty($oldcls)) {
			$oldcls .= ' ';
		}
		$oldcls .= $cls;
		$attrs['class'] = $oldcls;
	}
	
}
