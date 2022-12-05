<?php
/**
 * This is a helper class to calculate this and that required for rendering a pager
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetPagerCalculator implements IPolicyHolder {
	protected $data;
	protected $policy;
	/**
	 * @var IPagerAdapter
	 */
	protected $adapter;
		
	/**
	 * Returns item from data array
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get_data_item($key, $default) {
		return Arr::get_item($this->data, $key, $default);
	}

	/**
	 * Set pager data
	 *
	 * @param  array $data
	 * @return void
	 */
	public function set_data($data) {
		$this->data = $data;
		$this->adapter = $this->get_data_item('adapter', new PagerDefaultAdapter($data['page_data']));
	}
	
	/**
	 * Get policy
	 *
	 * @return int
	 */
	public function get_policy() {
		return $this->policy;
	}
	
	/**
	 * Set Policy
	 *
	 * @param int $policy
	 */
	public function set_policy($policy) {
		$this->policy = $policy;
	}

	/**
	 * Returns true, if client has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy) {
		return Common::flag_is_set($this->policy, $policy);	
	}

	/**
	 * Returns current page (one-based)
	 * @return int
	 */
	public function get_current_page() {
		return $this->data['page'];
	}
	
	/**
	 * Returns number of pages
	 * @var int
	 */
	public function get_total_pages() {
		return $this->data['pages_total'];
	}

	/**
	 * Returns whether a page is in the range of pages
	 * (that is 1 to total pages) or not
	 *
	 * @param int $page Page numbner
	 * @return bool
	 */
	public function is_valid_page($page) {
		return $page > 0 && $page <= $this->get_total_pages();
	}
	
	/**
	 * Returns previous link
	 *
	 * @param int $policy
	 * @param string $cls CSS class
	 * @return string
	 */
	public function get_previous_link($policy = WidgetPager::NONE, $cls = '') {
		$text = tr('&lt;&nbsp;Previous&nbsp;Page', array('app', 'core'));
		$link = Arr::get_item($this->data, 'previous_link', '');
		return $this->get_next_prev_link($link, $text, $cls, $policy);
	}
	
	/**
	 * Returns next link
	 *
	 * @param int $policy
	 * @param string $cls CSS class
	 * @return string
	 */
	public function get_next_link($policy = WidgetPager::NONE, $cls = '') {
		$text = tr('Next&nbsp;Page&nbsp;&gt;', array('app', 'core'));
		$link = Arr::get_item($this->data, 'next_link', '');
		return $this->get_next_prev_link($link, $text, $cls, $policy);
	}
	
	/**
	 * Returns array of page links
	 *
	 * @param int $total Total number of navigation links
	 * @param int $policy
	 * @param string $gap Text to use for marking gaps in navigation
	 * @return array
	 */
	public function get_page_links_array($total = 0, $policy = WidgetPager::NONE, $gap = '...') {
		// Build HTML
		$pages = $this->strip_pages($this->data['page'], $this->data['pages_total'], $total, $policy);
		$ret = array();
		foreach ($pages as $page) {
			$text = GyroString::escape($page['page']);
			if ($page['url']) {
				$ret[] = html::a($text, $page['url'], tr('Show page %page', array('app', 'core'), array('%page' => $text)));
			} else {
				if ($text === '') { 
					$ret[] = $gap; 
				}
				else {
					$ret[] = html::span($text, 'currentpage');
				}
			}
		}			
		return $ret;		
	}
	
	/**
	 * Returns snippet "page x of n"
	 *
	 * @param int $policy
	 * @param string $cls CSS class
	 * @return string
	 */
	public function get_page_x_of_n($policy = WidgetPager::NONE, $cls = '') {
		$policy |= $this->policy;
		$ret = '';
		if (!Common::flag_is_set($policy, WidgetPager::NO_PAGE_X_OF_N)) {
			$ret = tr('Page %page of %total', array('app', 'core'), array('%page' => $this->data['page'], '%total' => $this->data['pages_total']));
		}
		return $ret;		
	}
	
	/**
	 * Returns URL for page $page
	 */
	public function get_page_url($page) {
		$is_valid_page = $this->is_valid_page($page);
		$is_not_current = $page != $this->get_current_page();
		if ($is_not_current && $is_valid_page) {
			return $this->adapter->get_url_for_page($page);
		} else {
			return '';
		}
	}
	
	/**
	 * Returns link for page $page, with text $text, unless $page is 
	 * current page in which case it returns either nothing or 
	 * a span containing $text, depending on $policy
	 * 
	 * @param int $page Page, one-based
	 * @param string $text Link text
	 * @param string $cls CSS class
	 * @param int $policy Policy 
	 */
	public function get_page_link($page, $text, $cls = '', $policy = self::NONE) {
		$policy |= $this->policy;
		$ret = '';
		if ($page != $this->get_current_page()) {
			$ret .= html::a($text, $this->get_page_url($page), '', array('class' => $cls));
		}
		else if (!Common::flag_is_set($policy, WidgetPager::HIDE_INACTIVE_LINKS)) {
			$ret .= html::span($text, $cls);
		}		
		return $ret;				
	}
	
	/**
	 * Returns a next or prev link, depending on policy
	 *
	 * @param string $link
	 * @param string $text
	 * @param int $policy
	 * @return string
	 */
	protected function get_next_prev_link($link, $text, $cls, $policy) {
		$policy |= $this->policy;
		$ret = '';
		if ($link !== '') {
			$ret .= html::a($text, $link, '', array('class' => $cls));
		}
		else if (!Common::flag_is_set($policy, WidgetPager::HIDE_INACTIVE_LINKS)) {
			$ret .= html::span($text, $cls);
		}		
		return $ret;
	}

	
	/**
	 * Shortens pages to - say - 10 , laeving gaps
	 *
	 * @param array $pages_in
	 * @param int $current_page
	 * @param int $num_extract,
	 * @param int $policy
	 * @return array
	 */
	protected function strip_pages($current_page, $total_pages, $num_extract, $policy) {
		$gap = $this->create_page_array_item('');
		$policy |= $this->policy;
		$ret = array();
		// $current_page is one based, while array $pages_in is 0-based!
		$current_page_index = $current_page - 1;
		$total_pages_index = $total_pages - 1;
		
		$num_extract -= 1; // One is for current page!
		// Using ceil and floor leads to for example to the following setting
		// Given 10 links and current is 15: Links are 5 to 14 and 16 to 24
		// (Thats how google does it)
		$first_page_index = $current_page_index - ceil($num_extract / 2.0);
		$last_page_index = $current_page_index + floor($num_extract / 2.0);
		
		// Force total number of links, if wanted
		if ( !Common::flag_is_set($policy, WidgetPager::NO_FORCE_TOTAL_LINKS)) {
			if ($first_page_index < 0) {
				$last_page_index -= $first_page_index; // $first_page_index is negative!
				$first_page_index = 0;
			}
			if ($last_page_index > $total_pages_index) {
				$first_page_index -= ($last_page_index - $total_pages_index);
				$last_page_index = $total_pages_index;
			}
		}
		// Cut of if too large/small
		if ($first_page_index < 0) { $first_page_index = 0; }
		if ($last_page_index > $total_pages_index) { $last_page_index = $total_pages_index; }
		
		$strip = !Common::flag_is_set($policy, WidgetPager::NO_STRIP);
		if ($strip) {
			// Add leading link to page 1 and evetually some dots to mark the gap
			if ($first_page_index > 0) {
				$ret[] = $this->create_page_link_array_item(1);
			}
			if ($first_page_index > 1) {
				$ret[] = $gap;
			}		
		}

		// Pages before current
		for ($i = $first_page_index; $i < $current_page_index; $i++) {
			$ret[] = $this->create_page_link_array_item($i + 1);
		}
		// Current
		$ret[] = $this->create_page_array_item($current_page);
		// Pages after current
		for ($i = $current_page_index + 1; $i <= $last_page_index; $i++) {
			$ret[] = $this->create_page_link_array_item($i + 1);
		}
		
		if ($strip) {
			// Add link to last page and evetually some dots to mark the gap
			if ($last_page_index < $total_pages_index - 1) {
				$ret[] = $gap;
			}
			if ($last_page_index < $total_pages_index ) {
				$ret[] = $this->create_page_link_array_item($total_pages_index + 1);
			}		
		}

		return $ret;
	}	
	
	/**
	 * Returns page array item
	 *
	 * @param string $text
	 * @return array
	 */
	protected function create_page_array_item($text) {
		return array(
			'page' => $text,
			'url' => ''
		);
	}


	protected function create_page_link_array_item($page) {
		return array(
			'page' => $page,
			'url' => $this->get_page_url($page)
		);
	}
}

/**
 * Renders a pager
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetPager implements IWidget {
	const HIDE_INACTIVE_LINKS = 128;
	const NO_STRIP = 256;
	const NO_PAGE_X_OF_N = 512;
	const NO_FORCE_TOTAL_LINKS = 1024;
	const DONT_INDEX_PAGE_2PP = 2048;
	const DONT_CHANGE_TITLE = 4096;
	const DONT_ADD_BREADCRUMB = 8192;
	const ALSO_FOR_ONE_PAGE = 16384;

	public $data;
	
	public static function output($data, $policy = self::NONE) {
		$w = new WidgetPager($data);
		return $w->render($policy);
	}

	public function __construct($data) {
		$this->data = $data;
	}
	
	public function render($policy = self::NONE) {
		if ($policy == self::NONE) {
			$policy = Config::get_value(Config::PAGER_DEFAULT_POLICY, self::NONE);
		}

		if (Arr::get_item($this->data, 'pages_total', 0) <= 1) {
			if (!Common::flag_is_set($policy, self::ALSO_FOR_ONE_PAGE)) {
				return '';
			}
		}
		
		if (isset($this->data['policy'])) {
			$policy = $policy | ($this->data['policy'] * 2048); // Compatability
		}
		$calculator = Arr::get_item($this->data, 'calculator', Config::get_value(Config::PAGER_CALCULATOR));
		if (!$calculator instanceof WidgetPagerCalculator) {
			$calculator = new WidgetPagerCalculator();
		}
		
		$calculator->set_data($this->data);
		$calculator->set_policy($policy);
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/pager.meta');
		$view->assign('pager_calculator', $calculator);
		$view->assign('page_data', $this->data['page_data']);
		$view->render(); // this view should not return anything! 
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/pager.breadcrumb');
		$view->assign('pager_calculator', $calculator);
		$view->assign('page_data', $this->data['page_data']);
		$view->render(); // this view should not return anything! 
					
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/pager');
		$view->assign('pager_calculator', $calculator);
		$view->assign('page_data', $this->data['page_data']);
		return $view->render();
	}
}