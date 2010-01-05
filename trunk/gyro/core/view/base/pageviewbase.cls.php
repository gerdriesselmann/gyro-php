<?php
require_once dirname(__FILE__) . '/viewbase.cls.php';

/**
 * Base class for rendering a whole page
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class PageViewBase extends ViewBase {
	/**
	 * Page Data 
	 *
	 * @var PageData
	 */
	protected $page_data = null;

	public function __construct($page_data, $file = false) {
		$this->page_data = $page_data;
		$cacheid = $page_data->get_cache_manager()->get_cache_id();
		if (empty($file)) {
			$file = 'page';
		}
		parent::__construct($file, $cacheid);
	}


	/**
	 * Called before content is rendered, always
	 * 
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_preprocess($policy) {
		parent::render_preprocess($policy);
		// Change Template Path
		if (!empty($this->page_data->page_template)) {
			$this->template = $this->page_data->page_template;
		}
	}
	
	/**
	 * Sets content
	 * 
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function after_render(&$rendered_content, $policy) {
		parent::after_render($rendered_content, $policy);
		
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			header('Pragma: no-cache');
			header("Cache-Control: no-cache,no-store,private,max-age=0,must-revalidate");
			header('Last-Modified:');
			header('Expires: ' . GyroDate::http_date(time() - GyroDate::ONE_DAY));
		}		
	}	
	
	/**
	 * Called after content is rendered, always
	 * 
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_postprocess(&$rendered_content, $policy) {
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			$this->send_status();
		
			if (Config::has_feature(Config::GZIP_SUPPORT)) {
				header('Content-Encoding: gzip');
				$rendered_content = gzencode($rendered_content, 9);
			}
		}
	}
	
	/**
	 * Returns true, if cache should be used
	 *
	 * @return bool
	 */
	protected function should_cache() {
		$ret = parent::should_cache();
		if ($ret) {
			// Do not read cache, if a message should be or has been displayed
			$ret = empty($this->page_data->status->message);
		}
		return $ret;
	}	
	
	/**
	 * Returns cache life time in seconds
	 *
	 * @return int
	 */
	protected function get_cache_lifetime() {
		$cm = $this->page_data->get_cache_manager();
		if ($cm) {
			return $cm->get_expiration_datetime() - time(); 
		}
		else {
			return parent::get_cache_lifetime();
		}		
	}
	
	/**
	 * Write content to cache
	 *
	 * @param $cache_key
	 * @param $content
	 * @param $lifetime
	 */
	protected function store_cache($cache_key, $content, $lifetime) {
		$headers = array();
		$allowed = array(
			'content-type'
		);
		foreach(headers_list() as $h) {
			$h_test = strtolower($h);
			foreach($allowed as $a) {
				if (String::starts_with($h_test, $a)) {
					$headers[] = $h;
				}
			}
		}
		$cache_data = array(
			'status' => $this->page_data->status_code,
			'in_history' => $this->page_data->in_history,
			'headers' => $headers
		);
		Cache::store($cache_key, $content, $lifetime, $cache_data, false);
		$age = intval($lifetime / 10);
		$this->send_cache_headers(time(), time() + $lifetime, $age);
	}

	/**
	 * Read content from cache object
	 *
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @param $cache ICacheItem instance
	 * @return mixed
	 */
	protected function do_render_cache($cache, $policy) {
		$ret = '';
		if ($cache) {
			$cache_data = $cache->get_data();
			foreach(Arr::get_item($cache_data, 'headers', array()) as $header) {
				header($header);
			}
			$this->send_cache_headers($cache->get_creationdate(), $cache->get_expirationdate());
			$this->page_data->status_code = Arr::get_item($cache_data, 'status', '');
			if ($this->page_data->successful()) {
				// Send 304, if applicable, but only if site has 200 OK 
				Common::check_not_modified($cache->get_creationdate()); // exits if not modified
			}
			$this->page_data->in_history = Arr::get_item($cache_data, 'in_history', true);
			$ret = $cache->get_content_plain();
		}
		return $ret;
	}
	
	/**
	 * Send cache control headers for cache
	 *
	 * @param $lastmodified A timestamp 
	 * @param $expires A timestamp
	 * @param $max_age Max age in seconds
	 */
	protected function send_cache_headers($lastmodified, $expires, $max_age = 600) {
		$max_age = intval($max_age);
		header('Pragma:');
		header("Cache-Control: private, max-age=0, pre-check=0, must-revalidate");
		header('Last-Modified: ' . GyroDate::http_date($lastmodified));
		header('Expires: ' . GyroDate::http_date($expires));		
	}

	/**
	 * Assign variables
	 * 
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		parent::assign_default_vars($policy);
		
		$this->page_data->sort_blocks();	
		$this->assign('page_data', $this->page_data);
		$this->assign('pagetitle', $this->page_data->head->title);
		$this->assign('pagedescr', String::substr_word($this->page_data->head->description, 0, 200));
		$this->assign('status', $this->page_data->status);
		$this->assign('blocks', $this->page_data->blocks);
		$this->assign('content', $this->page_data->content);
		
		if ($this->page_data->router) {
			$this->assign('route_id', $this->page_data->router->get_route_id());
		}

		switch ($this->page_data->status_code) {
			case CONTROLLER_ACCESS_DENIED:
			case CONTROLLER_NOT_FOUND:
			case CONTROLLER_INTERNAL_ERROR:
				$error_view = ViewFactory::create_view(
					IViewFactory::MESSAGE, 
					'errors/' . String::plain_ascii($this->page_data->status_code, '_'),
					$this->page_data	
				);
				$this->page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
				$error_view->assign('page_data', $this->page_data);
				$this->assign('content', $error_view->render());				
				break;
			default:			
				break;
		}
	}
	
	/**
	 * Transform an error message into html
	 *
	 * @param $message Error message as string
	 * @return string
	 */
	protected function format_error($message) {
		return html::error($message);
	}

	/**
	 * Send status
	 */
	protected function send_status() {
		$log = Config::has_feature(Config::LOG_HTML_ERROR_STATUS);
		switch ($this->page_data->status_code) {
			case CONTROLLER_ACCESS_DENIED:
				Common::send_status_code(403); // Forbidden
				break;
			case CONTROLLER_NOT_FOUND:
				Common::send_status_code(404); //Not found
				break;
			case CONTROLLER_INTERNAL_ERROR:
				Common::send_status_code(503); // Service unavailable
				break;
			default:
				// OK, a valid page. This can be remembered, if allowed
				if ($this->page_data->in_history) {
					History::push(Url::current());
				}					
				$log = false;
				break;
		}		
		if ($log) {
			Load::components('referer', 'logger');
			$referer = Referer::current();
			$request = RequestInfo::current();
			$params = array(
				'code' => $this->page_data->status_code,
				'referer' => $referer->build(),
				'referer_org' => $referer->get_original_referer_url(),
				'useragent' => $request->user_agent(),
				'host' => $request->remote_host()
			);
			Logger::log('html_error_status', $params);
		}
	}
}
