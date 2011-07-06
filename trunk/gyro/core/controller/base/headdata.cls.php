<?php
define('ROBOTS_NOINDEX', 1);
define('ROBOTS_NOFOLLOW', 2);
define('ROBOTS_NOARCHIVE', 4);
define('ROBOTS_NOINDEX_NOFOLLOW', 7);
define('ROBOTS_NOINDEX_FOLLOW', 5);
define('ROBOTS_INDEX_FOLLOW', 0);

/**
 * Centralizes HTML HEAD data
 * 
 * Collects all stuff that is put in the HTML head section, like CSS, scripts
 * or meta information
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class HeadData implements IRenderer {
	/**
	 * Page Title
	 *
	 * @var string
	 */
	public $title;
	/**
	 * Page Meta Description
	 *
	 * @var string
	 */
	public $description = '';
	/**
	 * Page Meta Keywords
	 *
	 * @var string
	 */
	public $keywords = '';
	/**
	 * Robots policy
	 *
	 * @var int
	 */
	public $robots_index = ROBOTS_INDEX_FOLLOW;
	
	public $js_files = array();
	public $js_snippets = array('before' => array(), 'after' => array());
	public $css_files = array();
	public $css_snippets = array('before' => array(), 'after' => array());
	public $conditional_css_files = array();
	public $meta = array();
	public $meta_http_equiv = array();
	public $links = array();
	
	const META_INFORMATION = 256;
	const JAVASCRIPT_INCLUDES = 512;
	const CSS_INCLUDES = 1024;
	const ALL = 1792;
	
	const IE50 = 'IE5';
	const IE55 = 'IE55';
	const IE6 = 'IE6';
	const IE7 = 'IE7';
	
	public function __construct() {
		$this->title = Config::get_value(Config::TITLE);
	}
	
	/**
	 * Returns html
	 *
	 * @param int $policy Defines how to render, meaning depends on implementation
	 * @return string The rendered content
	 */
	public function render($policy = self::META_INFORMATION) {
		$ret = '';
		if (Common::flag_is_set($policy, self::META_INFORMATION)) {
			$ret .= $this->render_title($this->title, $this->description, $this->keywords);
			$ret .= $this->render_robots($this->robots_index);
			$ret .= $this->render_meta($this->meta);
			$ret .= $this->render_links($this->links);
			$ret .= $this->render_meta_http_equiv($this->meta_http_equiv);
		}
		if (Common::flag_is_set($policy, self::CSS_INCLUDES)) {
			$ret .= $this->render_css_snippets($this->css_snippets['before']);
			$ret .= $this->render_css($this->css_files);
			$ret .= $this->render_conditional_css($this->conditional_css_files);
			$ret .= $this->render_css_snippets($this->css_snippets['after']);
		}
		if (Common::flag_is_set($policy, self::JAVASCRIPT_INCLUDES)) {
			$ret .= $this->render_js($this->js_files);
		}
		return $ret;
	}

	/**
	 * Add a javascript file include 
	 * 
	 * @param string $file
	 * @param bool $to_front If true, the javascript file is included before other
	 * @return void
	 */
	public function add_js_file($file, $to_front = false) {
		if (!empty($file)) {
			if ($to_front) {
				array_unshift($this->js_files, $file);
			} else {
				$this->js_files[] = $file;
			}
		}
	}
	
	/**
	 * Add a javascript snippet
	 * 
	 * @param string $snippet
	 * @param bool $before_includes If true, the snippet is placed before include files
	 * @return void
	 */
	public function add_js_snippet($snippet, $before_includes = false) {
		if (!empty($snippet)) {
			if ($before_includes) {
				$this->js_snippets['before'][] = $snippet;
			} else {
				$this->js_snippets['after'][] = $snippet;
			}
		}
	}
	
	public function add_css_file($file, $to_front = false) {
		if (!empty($file)) {
			if ($to_front) {
				array_unshift($this->css_files, $file);
			} else {
				$this->css_files[] = $file;
			}
		}
	}

	public function add_conditional_css_file($browser, $file, $to_front = false) {
		if (!empty($file)) {
			if (!isset($this->conditional_css_files[$browser])) {
				$this->conditional_css_files[$browser] = array();
			}
			if ($to_front) {
				array_unshift($this->conditional_css_files[$browser], $file);
			} else {
				$this->conditional_css_files[$browser][] = $file;
			}
		}
	}
		
	/**
	 * Add a CSS snippet
	 *
	 * @param string $snippet
	 * @param bool $before_includes If true, the snippet is placed before included files
	 * @return void
	 */
	public function add_css_snippet($snippet, $before_includes = false) {
		if (!empty($snippet)) {
			if ($before_includes) {
				$this->css_snippets['before'][] = $snippet;
			} else {
				$this->css_snippets['after'][] = $snippet;
			}
		}
	}

	public function add_meta($name, $content, $override = false) {
		if ($override || !isset($this->meta[$name]) ) {
			$this->meta[$name] = $content;
		}
	}

	public function add_meta_http_equiv($name, $content) {
		$this->meta_http_equiv[$name] = $content;
	}
	
	/**
	 * Add a <link> to head
	 * 
	 * @param string $href Url link points to 
	 * @param string $rel rel attribute 
	 * @param array $attrs More attributes, like type, rev, etc
	 * @return void
	 */
	public function add_link($href, $rel, $attrs = array()) {
		$attrs = Arr::force($attrs, false);
		$attrs['rel'] = $rel;
		$attrs['href'] = $href;
		$this->links[$rel . $href] = html::tag_selfclosing('link', $attrs);
	}
	
	/**
	 * Render title and meta description section
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @return string
	 */
	protected function render_title($title, $description, $keywords) {
    	$ret = '';
    	// title
    	$ret .= html::tag('title', String::escape($title)) . "\n";
    	if ($description) {
    		$description = String::preg_replace('|\s\s+|s', ' ', $description);
    		$ret .= html::meta('description', String::substr_word($description, 0, 200)) . "\n";
    	}
    	if ($keywords) {
    		$ret .= html::meta('keywords', $keywords) . "\n";
    	}
    	return $ret;
	}
	
	/**
	 * Render META robots section
	 *
	 * @param int $robot_policy
	 * @return string
	 */
	protected function render_robots($robot_policy) {
    	$robots_policies = array();
    	$robots_available_policies = array(
			'index' => ROBOTS_NOINDEX,
    		'follow' => ROBOTS_NOFOLLOW,
    		'archive' => ROBOTS_NOARCHIVE    	
    	);
    	foreach ($robots_available_policies as $name => $flag) {
    		if (Common::flag_is_set($robot_policy, $flag)) {
    			$robots_policies[] = 'no' . $name;
    		}
    		else {
    			$robots_policies[] = $name;
    		}
    	}
    	
    	return html::meta('robots', implode(', ', $robots_policies)) . "\n";
	}
		
	/**
	 * Render Meta Tags
	 *
	 * @param array 
	 * @return string
	 */
	protected function render_meta($arr_meta) {
		$ret = '';
		foreach ($arr_meta as $name => $content) {
			$ret .= html::meta($name, $content) . "\n";
		}
		return $ret;
	}	
	
	/**
	 * Render Meta Http Quiv Tags
	 *
	 * @param array 
	 * @return string
	 */
	protected function render_meta_http_equiv($arr_meta) {
		$ret = '';
		foreach ($arr_meta as $name => $content) {
			$ret .= html::meta_http_equiv($name, $content)  . "\n";
		}
		return $ret;
	}	
	
	/**
	 * Render Links
	 *
	 * @param array 
	 * @return string
	 */
	protected function render_links($arr_links) {
		$ret = '';
		foreach ($arr_links as $name => $content) {
			$ret .= $content . "\n";
		}
		return $ret;
	}	
	
	/**
	 * Render CSS includes
	 *
	 * @param array CSS files
	 * @return string
	 */
	protected function render_css($css_files) {
		$ret = '';
		$css_files = array_unique($css_files);
		foreach ($css_files as $file) {
			$file = String::escape($this->escape_file($file));
			$ret .= "<link rel=\"stylesheet\" type=\"text/css\"  href=\"$file\" />\n";
		}
		return $ret;
	}
	
	protected function escape_file($file) {
		if (strpos($file, '://') === false) {
			$file = Config::get_value(Config::URL_BASEDIR) . ltrim($file, '/');
		}
		return $file;		
	}
	
	/**
	 * Render coditional CSS
	 * 
	 * @param array $arr_browsers
	 * @return string
	 */
	protected function render_conditional_css($arr_browsers) {
		$ret = '';
		foreach($arr_browsers as $browser => $css_files) {
			$token = '';
			switch ($browser) {
				case self::IE50:
					$token = 'IE 5.0';
					break;
				case self::IE55:
					$token = 'IE 5.5';
					break;
				case self::IE6:
					$token = 'IE 6';
					break;
				case self::IE7:
					$token = 'IE 7';
					break;
				default:
					continue;
					break; 
			}
			$ret .= "<!--[if $token]>\n" . $this->render_css($css_files) . "<![endif]-->\n";
		}
		return $ret;
	}

	/**
	 * Render CSS includes
	 *
	 * @param array CSS files
	 * @return string
	 */
	protected function render_js($js_files) {
		$js_files = array_unique($js_files);
		$ret = '';
		$ret .= $this->render_js_snippets($this->js_snippets['before']);
		foreach ($js_files as $file) {
			$file = $this->escape_file($file);
			$ret .= html::include_js($file) . "\n";
		}
		$ret .= $this->render_js_snippets($this->js_snippets['after']);
		return $ret;
	}
	
	protected function render_js_snippets($arr_snippets) {
		$ret = '';
		foreach($arr_snippets as $snippet) {
			$ret .= html::script_js($snippet) . "\n";	
		}
		return $ret;
	}

	protected function render_css_snippets($arr_snippets) {
		$ret = '';
		foreach($arr_snippets as $snippet) {
			$ret .= html::style($snippet) . "\n";
		}
		return $ret;
	}
}
