<?php
require_once dirname(__FILE__) . '/viewbase.cls.php';

/**
 * Base class for rendering a whole page
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class PageViewBase extends ViewBase {
	const POLICY_GZIP = 1024;
		
	/**
	 * Page Data 
	 *
	 * @var PageData
	 */
	protected $page_data = null;

	public function __construct(PageData $page_data, $file = false) {
		$this->page_data = $page_data;

		if (empty($file)) {
			$file = 'page';
		}
		parent::__construct($file, $page_data->get_cache_manager()->get_cache_id());
	}

	/**
	 * Process view and returnd the created content
	 *
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		if (Config::has_feature(Config::GZIP_SUPPORT)) {
			$policy |= self::POLICY_GZIP;
		}
		return parent::render($policy);
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
	protected function render_content(&$rendered_content, $policy) {
		parent::render_content($rendered_content, $policy);
		if (Common::flag_is_set($policy, self::POLICY_GZIP)) {
			$rendered_content = gzdeflate($rendered_content, 9);
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
			$cache_header_manager = $this->page_data->get_cache_manager()->get_cache_header_manager();
			$cache_header_manager->send_headers(
				$rendered_content, 
				$this->page_data->get_cache_manager()->get_expiration_datetime(), 
				$this->page_data->get_cache_manager()->get_creation_datetime()
			);
			
			if (Common::flag_is_set($policy, self::POLICY_GZIP)) {
				GyroHeaders::set('Content-Encoding', 'deflate', true);
			}
			GyroHeaders::set('Vary', 'Accept-Encoding', false);
			GyroHeaders::set('Date', GyroDate::http_date(time()), true);
			
			GyroHeaders::send();
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
			$ret = empty($this->page_data->status) || $this->page_data->status->is_empty();
		}
		return $ret;
	}	
	
	/**
	 * Returns cache life time in seconds
	 *
	 * @return int
	 */
	protected function get_cache_lifetime() {
		return $this->page_data->get_cache_manager()->get_expiration_datetime() - time();
	}
	
	/**
	 * Write content to cache
	 *
	 * @param $cache_key
	 * @param $content
	 * @param $lifetime
	 */
	protected function store_cache($cache_key, $content, $lifetime, $policy) {
		$headers = array();
		$forbidden = array(
			'age',
			'date',
			'content-encoding',
			'content-length',
			'server',
			'set-cookie',
			'transfer-encoding',
			'x-powered-by',
			'keep-alive',
			'connection'			
		);
		foreach(GyroHeaders::headers() as $name => $val) {
			if (!in_array($name, $forbidden)) {
				$headers[] = $val;
			}
		}
		$cache_data = array(
			'status' => $this->page_data->status_code,
			'in_history' => $this->page_data->in_history,
			'headers' => $headers,
			'cacheheadermanager' => $this->page_data->get_cache_manager()->get_cache_header_manager()
		);
		$gziped = Common::flag_is_set($policy, self::POLICY_GZIP);
		Cache::store($cache_key, $content, $lifetime, $cache_data, $gziped);
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
				GyroHeaders::set($header, false, true);
			}
			//$etag = Arr::get_item($cache_data, 'etag', '');
			$cache_header_manager = Arr::get_item($cache_data, 'cacheheadermanager', $this->page_data->get_cache_manager()->get_cache_header_manager());
			$this->page_data->get_cache_manager()->set_cache_header_manager($cache_header_manager);
			$this->page_data->status_code = Arr::get_item($cache_data, 'status', '');
			$this->page_data->in_history = Arr::get_item($cache_data, 'in_history', true);
			if (Common::flag_is_set($policy, self::POLICY_GZIP)) {
				$ret = $cache->get_content_compressed();
			}
			else {
				$ret = $cache->get_content_plain();
			}
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
	protected function send_cache_headers($lastmodified, $expires, $max_age = 600, $etag = '') {
		$max_age = intval($max_age);
		GyroHeaders::set('Pragma', '', false);
		GyroHeaders::set('Cache-Control', "private, must-revalidate, max-age=$max_age", false);
		GyroHeaders::set('Last-Modified', GyroDate::http_date($lastmodified), false);
		GyroHeaders::set('Expires', GyroDate::http_date($expires), false);
		GyroHeaders::set('Etag', $etag, true);		
	}

	/**
	 * Assign variables
	 * 
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		parent::assign_default_vars($policy);
		
		// Check for error templates and render error content, if required
		switch ($this->page_data->status_code) {
			case CONTROLLER_ACCESS_DENIED:
			case CONTROLLER_NOT_FOUND:
			case CONTROLLER_INTERNAL_ERROR:
				$error_view = ViewFactory::create_view(
					IViewFactory::CONTENT, 
					'errors/' . String::plain_ascii($this->page_data->status_code, '_'),
					$this->page_data	
				);
				$this->page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
				$error_view->render();				
				break;
			default:			
				break;
		}
		
		$this->page_data->sort_blocks();	
		$this->assign('page_data', $this->page_data);
		$this->assign('pagetitle', $this->page_data->head->title);
		$this->assign('pagedescr', String::substr_word($this->page_data->head->description, 0, 200));
		$this->assign('status', $this->page_data->status);
		$this->assign('blocks', $this->page_data->blocks);
		$this->assign('content', $this->page_data->content);
		$breadcrumb = is_string($this->page_data->breadcrumb) ? $this->page_data->breadcrumb : WidgetBreadcrumb::output($this->page_data->breadcrumb);
		if (Config::get_value(Config::VERSION_MAX) < 0.6) {
			// In 0.5 PageData::breadcrumb is alwys the result of WidgetBreadcrumb 
			$this->page_data->breadcrumb = $breadcrumb;
		}
		$this->assign('breadcrumb', $breadcrumb);
		
		if ($this->page_data->router) {
			$this->assign('route_id', $this->page_data->router->get_route_id());
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
