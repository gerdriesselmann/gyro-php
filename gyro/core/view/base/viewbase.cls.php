<?php
Load::models('cache');

/**
 * Basic genric view implementation
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class ViewBase implements IView, ICache {
	const EVENT_BEFORE_RENDER = 'view_before_render';
	const EVENT_AFTER_RENDER = 'view_after_render';

	/**
	 * Cached content
	 *
	 * @var ICacheItem
	 */
	protected $cached_content = null;
	/**
	 * Assigned vars
	 *
	 * @var array
	 */
	protected $vars = array();
	/**
	 * Cache ID
	 *
	 * @var mixed
	 */
	protected $cache_id;
	/**
	 * Key to retrieve template to render
	 *
	 * @var string
	 */
	protected $template;

	public function __construct($template, $cache_id = '') {
		$this->template = $template;
		$this->set_cache_id($cache_id);
	}
	
	/**
	 * Creates a view of type IViewFactory::MESSAGE and given template
	 * and copies all variables set on this view  
	 * 
	 * @param string $template 
	 * @return IView
	 */
	public function create_child_view($template) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, $template);
		$this->copy_to($view);
		return $view;
	}

	/**
	 * Pass a variable to the view
	 *
	 * @param string $var The name of the variable as string
	 * @param mixed $value The value
	 */
	public function assign($var, $value) {
		$this->vars[$var] = $value;
	}

	/**
	 * Pass an associative array to the view
	 *
	 * @param array $vars Associative array of variable names and values
	 */
	public function assign_array($vars) {
		$this->vars = array_merge($this->vars, $vars);
	}
	
	/**
	 * Retrieve a variable from the view
	 *
	 * @param string $var The name of the variable as string
	 * @return mixed The Value
	 */
	public function retrieve($var) {
		return Arr::get_item($this->vars, $var, false);	
	}
	
	/**
	 * Retrieve all variables
	 * 
	 * @return array Associative array 
	 */
	public function retrieve_array() {
		return $this->vars;
	}
	
	/**
	 * Copy all variables to other view
	 * 
	 * @param IView $view
	 */
	public function copy_to($view) {
		$view->assign_array($this->vars);
	}
		
	/**
	 * Returns cache id
	 *
	 * @return mixed
	 */
	public function get_cache_id() {
		return $this->cache_id;
	}

	/**
	 * Set cache id
	 *
	 * @param string|array $cacheid A cache id, a string or an array
	 */
	public function set_cache_id($cacheid) {
		$this->cache_id = $cacheid;
	}
	
	/**
	 * Returns true, if cache should be used
	 *
	 * @return bool
	 */
	protected function should_cache() {
		return !empty($this->cache_id);
	}
	
	/**
	 * Returns true, if a cache entry exists 
	 *
	 * @return bool
	 */
	public function is_cached() {
		return ($this->get_cache_object() !== false);
	}
	
	/**
	 * Reads the cache
	 *
	 * @return ICacheItem
	 */
	protected function get_cache_object() {
		if (is_null($this->cached_content)) {
			$this->cached_content = $this->should_cache() ? $this->read_cache($this->cache_id) : false;
		}
		return $this->cached_content;
	}
	
	/**
	 * Returns cache life time in seconds
	 *
	 * @return int
	 */
	protected function get_cache_lifetime() {
		return 2 * GyroDate::ONE_HOUR; // 2 hours 
	}

	/**
	 * Process view and returnd the created content
	 * 
	 *  The control flow on rendering is as follows:
	 * 
	 * A) If item is not cached or caching is disabled
	 * 
	 * 1. render_preprocess
	 * 2. render_from_cache
	 * 3. do_render 
	 *   3.1 before_render
	 *   3.2 render_content
	 *   3.3 after_render
	 * 4. update_cache
	 *   4.1 store_cache (if item should be cached)
	 * 5. render_postprocess
	 * 
	 * B) If item is cached and caching is enabled
	 * 
	 * 1. render_preprocess
	 * 2. render_from_cache 
	 * 2.1  do_render_cache 
	 * 3. update_cache
	 * 4. render_postprocess
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$this->render_preprocess($policy);
		
		$ret = '';
		$ret = $this->render_from_cache($policy);
		// Cache may be empty, so check that
		if (empty($ret)) {
			$ret = $this->do_render($policy);
		}
		$this->update_cache($ret, $policy);
		$this->render_postprocess($ret, $policy);
		
		if (Common::flag_is_set($policy, self::DISPLAY)) {
			print $ret;
		}
		return $ret;
	}
	
	/**
	 * Retrieve item from cache, if cached and caching is enabled
	 * 
	 * @param int $policy
	 * @return mixed
	 */
	protected function render_from_cache($policy) {
		$ret = '';
		if (!Common::flag_is_set($policy, self::NO_CACHE) && $this->is_cached()) {
			$ret = $this->do_render_cache($this->get_cache_object(), $policy);
		}
		return $ret;
	}
	
	/**
	 * Store item in cache, if required
	 * 
	 * @param string $data
	 * @param int $policy
	 */
	protected function update_cache(&$data, $policy) {
		if (!Common::flag_is_set($policy, self::NO_CACHE)
			&& !$this->is_cached() 
			&& $this->should_cache()) 
		{
			$this->store_cache($this->cache_id, $data, $this->get_cache_lifetime(), $policy);
		}		
	}
	
	/**
	 * Render
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return mixed
	 */
	protected function do_render($policy) {
		$timer = new Timer();
		
		$this->assign_default_vars($policy);
		$ret = '';
		$this->before_render($ret, $policy);
		$this->render_content($ret, $policy);
		$this->after_render($ret, $policy);
		return $ret;
	}

	/**
	 * Called before content is rendered, always
	 * 
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_preprocess($policy) {
	}

	/**
	 * Called after content is rendered, always
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_postprocess(&$rendered_content, $policy) {
	}
	
	/**
	 * Called before content is rendered, but not if content is taken from cache
	 * 
	 * @param string $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function before_render(&$rendered_content, $policy) {
		EventSource::Instance()->invoke_event(self::EVENT_BEFORE_RENDER, array('view' => $this, 'policy' => $policy), $rendered_content);
	}

	/**
	 * Called after content is rendered, but not if content is taken from cache
	 * 
	 * @param string $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function after_render(&$rendered_content, $policy) {
		EventSource::Instance()->invoke_event(self::EVENT_AFTER_RENDER, array('view' => $this, 'policy' => $policy), $rendered_content);
	}
	
	/**
	 * Sets content
	 * 
	 * @param string $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_content(&$rendered_content, $policy) {
		$arr_engine = $this->split_template($this->template);
		$template_engine = $this->create_template_engine($arr_engine['engine']);
		$template_engine->assign_array($this->vars);
		$rendered_content = $template_engine->fetch($arr_engine['resource']);
	}
	
	/**
	 * Splites templates into engine and resource
	 *
	 * @param string $template String of type engine:resource
	 */
	protected function split_template($template) {
		$arr_ret = array(
			'engine' => Config::get_value(Config::DEFAULT_TEMPLATE_ENGINE),
			'resource' => $template
		);
		$pos_colon = is_string($template) ? strpos($template, '::') : false;
		if ($pos_colon !== false) {
			// There is a "::" in the template file name, so use this as protocol
			// Example core::page => $engine = core, $template = page 
			$arr_ret['engine'] = substr($template, 0, $pos_colon);
			$arr_ret['resource'] = substr($template, $pos_colon + 2);
		}
		return $arr_ret;		
	}
	
	/**
	 * Creates a template engine based on template type
	 * 
	 * The template engine to choose is detected by a template protocol. E.g.
	 * "core::template" will use the core template engine, while
	 * "myengine::template" will use "myengine". If the protocol is ommitet, 
	 * DEFAULT_TEMPLATE_ENGINE is used.
	 *
	 * @param string $engine A string specifying the engine
	 * @return ITemplateEngine
	 */
	protected function create_template_engine($engine) {
		Load::directories('view/templateengines/' . $engine);
		require_once dirname(__FILE__) . '/templatepathresolver.cls.php';
		$cls = 'TemplateEngine' . ucfirst($engine); // $engine is supposed to be ASCII
		return new $cls();
	}
	
	/**
	 * Write content to cache
	 *
	 * @param string|array $cache_key Either a string or an array
	 * @param string $content Content to cache as string
	 * @param int $lifetime Lifetime in seconds
	 */
	protected function store_cache($cache_key, $content, $lifetime, $policy) {
		Cache::store($cache_key, $content, $lifetime);
	}
	
	/**
	 * Retrieve ressource from cache
	 *
	 * @param string $cache_key Either a string or an array
	 * @return ICacheItem
	 */
	protected function read_cache($cache_key) {
		return Cache::read($cache_key);
	}
	
	/**
	 * Read content from cache object
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @param ICacheItem $cache ICacheItem instance
	 * @return mixed
	 */
	protected function do_render_cache($cache, $policy) {
		return ($cache) ? $cache->get_content_plain() : '';
	}
	
	/**
	 * Assign all default vars
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		$this->assign('self', $this);
		$this->assign('appname', Config::get_value(Config::TITLE));
		$this->assign('baseurl', Config::get_value(Config::URL_BASEDIR));
		$this->assign('baseurl_ssl', Config::get_url(Config::URL_BASEURL_SAFE));
		$this->assign('baseurl_http', Config::get_url(Config::URL_BASEURL));
		$this->assign('url_self', Url::current()->clear_query()->build(Url::RELATIVE));
		$this->assign('applang', GyroLocale::get_language());
		$this->assign('appcharset', GyroLocale::get_charset());
		
		$imageurl = Config::get_url(Config::URL_IMAGES);
		$this->assign('imageurl', $imageurl);
		if (strpos($imageurl, '//') === false) {
			$imageurl = Config::get_url(Config::URL_SERVER) . $imageurl;
		}
		$this->assign('imageurl_http', $imageurl);
	}
}
