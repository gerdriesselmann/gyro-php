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

		if (Url::current()->is_ancestor_of($url)) {
	  		html::_appendClass($attrs, 'active');
		}
		if (Url::current()->is_same_as($url)) {
	  		html::_appendClass($attrs, 'self');
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
	 * 
	 * @return String The HTML code for title and meta tags
	 */
	public static function title($text, $descr = "") {
		$arrFilter = array("-", ",", ".", "(", ")", ":", "'", '"', "&");
		$keywords = str_replace($arrFilter, " ",  $descr);
		$keywords = str_replace("  ", " ", $keywords);
		$keywords = str_replace(" ", ",", $keywords);

		$ret = self::tag('title', GyroString::escape($text)) . "\n";
		$ret .= self::meta('title', $text) . "\n";
		$ret .= self::meta('description', $descr) . "\n";
		$ret .= self::meta('keywords', $keywords) . "\n";
		return $ret;
	}

	/**
	 * Output image tag
	 * 
	 * @param string $path URL of image
	 * @param string $alt Alt text
	 * @param array $attrs HTML attributes
	 * 
	 * @return string 
	 */
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
	 * @return String The HTML Code for outputting an error
	 *
	 * @deprecated Use WidgetAlert instead
	 */
	public static function error($text) {
		return WidgetAlert::output($text, WidgetAlert::ERROR);
	}

	/**
	 * Static. Returns $text formatted as success message.
	 *
	 * @param String The Message
	 * @return String The HTML Code for outputting a success message
	 *
	 * @deprecated Use WidgetAlert instead
	 */
	public static function success($text) {
		return WidgetAlert::output($text, WidgetAlert::SUCCESS);
	}

	/**
	 * Static. Returns $text formatted as notification message.
	 *
	 * @param String The Message
	 * @return String The HTML Code for outputting a notification message
	 *
	 * @deprecated Use WidgetAlert instead
	 */
	public static function note($text) {
		return WidgetAlert::output($text, WidgetAlert::NOTE);
	}

	/**
	 * Static. Returns $text formatted as warning message.
	 *
	 * @param String The Message
	 * @return String The HTML Code for outputting a warning message
	 *
	 * @deprecated Use WidgetAlert instead
	 */
	public static function warning($text) {
		return WidgetAlert::output($text, WidgetAlert::WARNING);
	}

	/**
	 * Static. Returns $text formatted as information message.
	 *
	 * @param String The Message
	 * @return String The HTML Code for outputting an information message
	 *
	 * @deprecated Use WidgetAlert instead
	 */
	public static function info($text) {
		return WidgetAlert::output($text, WidgetAlert::INFO);
	}

	/**
	 * A div tag
	 * 
	 * @param string $text Content within div
	 * @param string $cls HTML class
	 * 
	 * @return string <div class="$cls>$text</div>
	 */
	public static function div($text, $cls = '') {
		return html::tag('div', $text, array('class' => $cls));
	}

	/**
	 * A p tag
	 * 
	 * @param string $text Content within tag
	 * @param string $cls HTML class
	 * 
	 * @return string <p class="$cls>$text</p>
	 */
	public static function p($text, $cls = '') {
		return html::tag('p', $text, array('class' => $cls));
	}

	/**
	 * A span tag
	 * 
	 * @param string $text Content within tag
	 * @param string $cls HTML class
	 * 
	 * @return string <span class="$cls>$text</span>
	 */
	public static function span($text, $cls = '') {
		return html::tag('span', $text, array('class' => $cls));
	}


	/**
	 * A strong tag
	 * 
	 * @param string $text Content within tag
	 * @param string $cls HTML class
	 * 
	 * @return string <strong class="$cls>$text</strong>
	 */
	public static function b($text, $cls = "") {
		return html::tag('strong', $text, array('class' => $cls));
	}

	/**
	 * An em tag
	 * 
	 * @param string $text Content within tag
	 * @param string $cls HTML class
	 * 
	 * @return string <em class="$cls>$text</em>
	 */
	public static function em($text, $cls = "") {
		return html::tag('em', $text, array('class' => $cls));
	}
	
	/**
	 * A heading
	 * 
	 * @param string $text Content within tag
	 * @param int $level The level of heading (1 to 6). 1 creates h1, 2 creates h2 etc... 
	 * @param string $cls HTML class
	 * 
	 * @return string <h$level class="$cls>$text</h$level>
	 */
	public static function h($text, $level, $cls="") 	{
		return html::tag('h' . $level, $text, array('class' => $cls));
	}

	/**
	 * Tag surrounding text
	 * 
	 * @param string $tag Tag name
	 * @param string $text Content within tag
	 * @param array $attrs HTML attributes as associative array of name => value
	 * 
	 * @return string <tag attrs>text</tag>
	 */
	public static function tag($tag, $text, $attrs = array()) {
		return '<' . $tag . html::attrs($attrs) . ">" . $text . "</" . $tag . ">";
	}

	/**
	 * Self closing tag
	 * 
	 * @param string $tag Tag name
	 * @param array $attrs HTML attributes as associative array of name => value
	 * 
	 * @return string <tag attrs />
	 */
	public static function tag_selfclosing($tag, $attrs = array()) {
		return '<' . $tag . html::attrs($attrs) . " />";
	}


	/**
	 * Returns HTML code for a list
	 * 
	 * The list's items are provided with special classes:
	 * 
	 * - Even/uneven
	 * - First and last 
	 *
	 * @param array $items Array of list items
	 * @param string $cls Possible class name. The class is assigned to both items and container (ul/ol)
	 * @param Boolean True if list should be ordered, that is coantiner shouldbe ol not ul
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
	 * Returns HTML code for a definition list
	 * 
	 * The list topics and descriptions are provided with special classes:
	 * 
	 * - Even/uneven
	 * - First and last 
	 *
	 * @param array $items Array of list items as associative array with topic => description
	 * @param string $cls Possible class name. The class is assigned to topics, descriptions, and container
	 * @return String
	 */
	public static function dl($items, $cls = '') {
		$c = count($items);
		if ($c == 0) {
			return ''; 
		}
		
		$list = "\n";
		$i = 0;
		foreach($items as $dt => $dd) {
			$arr_cls = array($cls);
			$arr_cls[] = (++$i % 2) ? 'uneven' : 'even';
			if ($i === 1) {
				$arr_cls[] = 'first';
			}
			if ($i === $c) {
				$arr_cls[] = 'last';
			}
			$list .= html::tag('dt', $dt, array('class' => $arr_cls));
			$list .= "\n";
			$list .= html::tag('dd', $dd, array('class' => $arr_cls));
			$list .= "\n";			
		}

		return html::tag('dl', $list, array('class' => $cls));
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
		$opts = self::options(Arr::force($options), Arr::force($value));
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
			foreach($selected_values as $v) {
				// Try to cope with "" == 0 and such
				if ($key === '' || $v === '') {
					$match = ($key === $v);
				} else {
					$numeric_key = is_numeric($key);
					$numeric_v = is_numeric($v);
					if ($numeric_key xor $numeric_v) {
						$match = false;
					} else {
						$match = ($key == $v);
					}
				}
				if ($match) {
					$opt_attrs['selected'] = 'selected';
					break;
				}
			}
			if ($key === '') {
				$key = self::EMPTY_ATTRIBUTE;
			}
			$opt_attrs['value'] = $key;
			$ret = html::tag('option', GyroString::escape($display), $opt_attrs);
		} 		
		return $ret;
	}
	 
	/**
	 * Returns code for attribute, including leading blank.
	 *
	 * @return string Empty string if passed value is empty
	 */
	public static function attr($name, $value) {
		if (is_string($value)) {
			$value = str_replace("\n", ' ', $value);
		}
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

		return ' ' . $clean_name . '="' . GyroString::escape($value) . '"';
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

	/**
	 * A javascript include
	 * 
	 * @param string $path URL of script
	 * @return string <script type="text/javascript" src="$path"></script>
	 */
	public static function include_js($path) {
		return '<script type="text/javascript" src="' . GyroString::clear_html($path) . '"></script>';
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
	
	/**
	 * A CSS include
	 * 
	 * @param string $path URL of CSS
	 * @param string $media Media
	 * @return string <style type="text/css" media="$media">@import url($path);</style>
	 */
	public static function include_css($path, $media = 'screen') {
		return '<style type="text/css" media="' . GyroString::clear_html($media) . '">@import url(' . GyroString::clear_html($path) . ');</style>';
	}

	/**
	 * Create style tag with content
	 *
	 * @param string $content The CSS
	 * @return string
	 */
	public static function style($content) {
		$attrs = array(
			'type' => 'text/css'
		);
		return html::tag('style', $content, $attrs);
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
	 * Code is XHTML compliant!
	 * 
	 * @param string $file URL of flash file
	 * @param array $attrs HTML attributes for object tag
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
		
		self::_appendClass($attrs, 'player');
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
		$attr = Arr::force($attr, false);
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
	 * @param array $foot Array of arrays of foot cells. Cells must be already formated with either <td> or <th>
	 *
	 * @return string
	 */
	public static function table($rows, $head, $summary, $attr = array(), $foot = array()) {
		$ret = '';
		
		$body = self::table_build_rows($rows);
		$head = self::table_build_rows($head);
		$foot = self::table_build_rows($foot);

		$content = '';
		$content .= ($head) ? html::tag('thead', $head) . "\n" : '';
		$content .= ($body) ? html::tag('tbody', $body) . "\n" : '';
		$content .= ($foot) ? html::tag('tfoot', $foot) . "\n" : '';

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
	private static function table_build_rows($rows) {
		$ret = '';
		$i = 0;
		$c = count($rows);

		// Test if $rows is array or array of arrays;
		if ($c && !is_array(reset($rows))) {
			$rows = array($rows);
		}

		// Iterate and output
		foreach($rows as $row) {
			$arr_cls = array();

			// row is
			// EITHER array("content" => $content, "class" => $class)
			// OR array of strings
			if (array_key_exists('content', $row)) {
				$cells = $row['content'];
				$cls = Arr::get_item($row, 'class', '');
				if ($cls) {
					$arr_cls[] = $cls;
				}
			} else {
				$cells = $row;
			}

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
