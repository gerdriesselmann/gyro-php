<?php
Load::models('cache');

/**
 * Basic genric view implementation
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class ViewBase implements IView, ICache {
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
	 * Creates a view of type IViewFactory::MESSAGE and given templaet
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
	 * @param $var The name of the variable as string
	 * @param $value The value
	 */
	public function assign($var, $value) {
		$this->vars[$var] = $value;
	}

	/**
	 * Pass an associative array to the view
	 *
	 * @param $vars Associative array of variable names and values
	 */
	public function assign_array($vars) {
		$this->vars = array_merge($this->vars, $vars);
	}
	
	/**
	 * Retrieve a variable from the view
	 *
	 * @param $var The name of the variable as string
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
	 * @param $cacheid A cache id, a string or an array
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
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$this->render_preprocess($policy);
		
		$ret = '';
		$cache_enabled = !Common::flag_is_set($policy, self::NO_CACHE); 
		if ($cache_enabled && $this->is_cached()) {
			$chache = $this->get_cache_object();
			$ret = $this->do_render_cache($this->get_cache_object(), $policy);
		}
		// Cache may be empty, so check that
		if (empty($ret)) {
			$ret = $this->do_render($policy);
			if ($cache_enabled && $this->should_cache()) {
				$this->store_cache($this->cache_id, $ret, $this->get_cache_lifetime(), $policy);
			}			
		}
		$this->render_postprocess($ret, $policy);
		
		if (Common::flag_is_set($policy, self::DISPLAY)) {
			print $ret;
		}
		return $ret;
	}
	
	/**
	 * Render
	 *
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
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
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function before_render(&$rendered_content, $policy) {
		EventSource::Instance()->invoke_event('view_before_render', array('view' => $this, 'policy' => $policy), $rendered_content);
	}

	/**
	 * Called after content is rendered, but not if content is taken from cache
	 * 
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function after_render(&$rendered_content, $policy) {
		EventSource::Instance()->invoke_event('view_after_render', array('view' => $this, 'policy' => $policy), $rendered_content);
	}
	
	/**
	 * Sets content
	 * 
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
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
	 * @param $template String of type engine:resource
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
	 * @param $engine A string specifying the engine
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
	 * @param $cache_key Either a string or an array
	 * @param $content Content to cache as string
	 * @param $lifetime Lifetime in seconds
	 */
	protected function store_cache($cache_key, $content, $lifetime, $policy) {
		Cache::store($cache_key, $content, $lifetime);
	}
	
	/**
	 * Retrieve ressource from cache
	 *
	 * @param $cache_key Either a string or an array
	 * @return ICacheItem
	 */
	protected function read_cache($cache_key) {
		return Cache::read($cache_key);
	}
	
	/**
	 * Read content from cache object
	 *
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @param $cache ICacheItem instance
	 * @return mixed
	 */
	protected function do_render_cache($cache, $policy) {
		return ($cache) ? $cache->get_content_plain() : '';
	}
	
	/**
	 * Assign all default vars
	 *
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
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
