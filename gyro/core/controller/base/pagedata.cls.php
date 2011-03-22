<?php
/**
 * Collects data used to render a page and to be exchange between different 
 * parts of the application
 * 
 * One instance of PageData is passed around in all steps of the rendering 
 * process.
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class PageData {
	/**
	 * Page content (HTML body)
	 *
	 * @var string (HTML)
	 */
	public $content = '';
	/**
	 * Head Data
	 * 
	 * @var HeadData
	 */
	public $head;
	/**
	 * Breadcrumb as HTML
	 * 
	 * @var string
	 */
	public $breadcrumb;
	/**
	 * Status to display
	 *
	 * @var Status
	 */
	public $status;
	/**
	 * Controller action response
	 *
	 * @var mixed
	 */
	public $status_code = CONTROLLER_OK;
	/**
	 * Pathstack, traces invoked URL 
	 *
	 * @var PathStack
	 */
	public $pathstack = null; // PathStack!
	/**
	 * Array of blocks 
	 *
	 * @var array Array of BlockBase
	 */
	public $blocks = array(); 
	/**
	 * True to historize this page
	 *
	 * @var bool
	 */
	public $in_history = true;
	/**
	 * Current Router
	 *
	 * @var RouterBase
	 */
	public $router = null;
	/**
	 * Traced array of GET parameters
	 *
	 * @var TracedArray
	 */
	private $get = null;
	/**
	 * Traced array of POST parameters
	 *
	 * @var TracedArray
	 */
	private $post = null;
	/**
	 * Cache Manager
	 *
	 * @var ICacheManager
	 */
	protected $cache_manager = null;
	/**
	 * Renderer Decorators to overload rendering behaviour for all routes
	 *
	 * @var array Array of IRenderDecorator class names
	 */
	protected $render_decorator_classes = array();
	/**
	 * Page template
	 */
	public $page_template;

	/**
	 * Constructor
	 *
	 * @param ICacheManager $cache_manager
	 * @param array $get Usually $_GET
	 * @param array $post Usually $_POST
	 */
	public function __construct($cache_manager, $get, $post) {
		$this->page_template = Config::get_value(Config::PAGE_TEMPLATE);
		$this->status = Status::restore();
		$this->head = new HeadData();
		//if (empty($this->status)) {
		//	$this->status = new Status();
		//}
		if (empty($cache_manager)) { $cache_manager = new NoCacheCacheManager(); } 
		$this->set_cache_manager($cache_manager);
		$this->pathstack = new PathStack();
		
		// GET array
		$tmp = Arr::force($get);
		array_walk_recursive($tmp, array($this, 'trim_array_content'));
		// No path param, since this defines url
		unset($tmp[Config::get_value(Config::QUERY_PARAM_PATH_INVOKED)]);
		$this->get = new TracedArray($tmp);
		
		// POST array
		$tmp = Arr::force($post);
		array_walk_recursive($tmp, array($this, 'trim_array_content'));
		$this->post = new TracedArray($this->preprocess_post($tmp));
	}
	
	public function __clone() {
		// Force a copy of this->object, otherwise
		// it will point to same object.
		$this->cache_manager = clone($this->cache_manager);
		$this->get = clone($this->get);
		$this->post = clone($this->post);
		$this->head = clone($this->head);
		$this->pathstack = clone($this->pathstack);
		if ($this->status) {
			$this->status = clone($this->status);
		}        
    }	
	
	/**
	 * Function to be called to trim arrays passed 
	 *
	 * @param string $item Value 
	 * @param string $key Key 
	 */
	private function trim_array_content(&$item, $key) {
		$item = trim($item);
	}

	/**
	 * Preprocess POST data
	 *
	 * @param array $arr
	 */
	private function preprocess_post($arr) {
		$arr = $this->clear_button_parameters($arr);
		$arr = $this->merge_uploaded_files($arr);
		return $arr;
	}
	
	/**
	 * Write data from $_FILES into our post array
	 *
	 * @param unknown_type $arr
	 */
	private function merge_uploaded_files($arr) {
		// We mus transform array of type 
		// name => key [=> name => name => ..] => value
		// into 
		// name [=> name => name => ..] => key => value
		$ret = array();
		foreach($_FILES as $name => $file_data) {
			$tmp = array();
			foreach($file_data as $key => $value) {
				if (is_array($value)) {
					array_walk_recursive($value, array($this, 'callback_transform_files'), $key);
					$value = Arr::force_keys_to_string($value);
					$tmp = array_merge_recursive($tmp, $value);
				}
				else {
					$tmp[$key] = $value;				
				}
			}
			$ret[$name] = $tmp;
		}
		$ret = Arr::unforce_keys_from_string($ret);	
		return array_merge_recursive($arr, $ret);		
	}
	
	private function callback_transform_files(&$item, &$key, $userdata = false) {
		$item = array($userdata => $item);
	}
	
	/**
	 * IE submists input type=image with name_x=x name_y=y, while all other browsers do 
	 * name=value. Fix this.
	 */
	private function clear_button_parameters($arr) {
		$candidates = array();
		foreach($arr as $key => $value) {
			// If there is key_x and key_y, add key to post array
			if (String::ends_with($key, '_x')) {
				$newkey = String::substr($key, 0, String::length($key) - 2);
				if (array_key_exists($newkey . '_y', $this->post)) {
					$candidates[$newkey] = 'computed';
				}
			}
		}
		// remove key_x and key_y
		foreach ($candidates as $key => $value) {
			unset($arr[$key . '_x']);
			unset($arr[$key . '_y']);
		}

		return array_merge($arr, $candidates);
	}
	
	/**
	 * Return POST data
	 *
	 * @return TracedArray
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Return GET data
	 *
	 * @return TracedArray
	 */
	public function get_get() {
		return $this->get;
	}

	/**
	 * Returns true if data has been posted
	 *
	 * @return bool
	 */
	public function has_post_data() {
		return $this->post->count() > 0;
	} 
	
	/**
	 * Returns the raw data send along with a request (for POST, PUT, DELETE)
	 * 
	 * @return string
	 */
	public function raw_request_body() {
		//$ret = Arr::get_item($GLOBALS, 'HTTP_RAW_POST_DATA', '');
		$ret = trim(file_get_contents('php://input'));
		return $ret;
	}	
	
	/**
	 * Returns cache manager
	 *
	 * @return ICacheManager
	 */
	public function get_cache_manager() {
		return $this->cache_manager;
	}
	
	/**
	 * Set cache manager
	 *
	 * @param ICacheManager $cache_manager
	 */
	public function set_cache_manager($cache_manager) {
		if (!empty($cache_manager)) {
			$this->cache_manager = $cache_manager;
			$this->cache_manager->initialize($this);
		}
	}
	
	/**
	 * Add a render decorator to overload defazlt render behaviour
	 *
	 * @param string $dec Class name of render decorator
	 */
	public function add_render_decorator_class($dec) {
		$this->render_decorator_classes[] = $dec;
	}
	
	/**
	 * Returns array of render decorators to overload default render behaviour
	 *
	 * @param IDispatcher Dispatcher
	 * @return array Array of IRenderDecorators
	 */
	public function get_render_decorators($route) {
		$ret = array();
		foreach($this->render_decorator_classes as $class) {
			$ret[] = new $class($route);
		}
		return $ret;
	}

	/**
	 * Set current url path
	 *
	 * @param strin $path
	 */
	public function set_path($path) {
		$this->pathstack->set_path($path);
	}

	/**
	 * Returns path stack
	 *
	 * @return Pathstack
	 */
	public function get_pathstack() {
		return $this->pathstack;
	}

	/**
	 * Add a block
	 *
	 * @param BlockBase $block
	 */
	public function add_block($block, $position = false, $weight = false) {
		if ($block->is_valid()) {
			if (!empty($position)) {
				$block->set_position($position);
			}
			if (!empty($weight)) {
				$block->set_index($weight);
			}
			$this->blocks[] = $block;
		}
	}
	
	/**
	 * Sort blocks
	 */
	public function sort_blocks() {
		if (function_exists('gyro_block_sort')) {
			usort($this->blocks, 'gyro_block_sort');
		}		
	}
	
	/**
	 * Get blocks for given position 
	 * 
	 * @param string $position Pass FALSE to retrieve all blocks 
	 */
	public function get_blocks($position = false) {
		$ret = array();
		foreach ($this->blocks as $block) {
			if ($position && $position != $block->get_position()) {
				continue;
			}
			$ret[] = $block;
		}		
		return $ret;
	}
	
	/**
	 * Set error 
	 *
	 * @param Status|string $msg Error or error message
	 */
	public function error($msg) {
		if (empty($this->status)) {
			$this->status = new Status();
		}
		if ($msg instanceof Status) {
			$this->status->merge($msg);
		}
		else {
			$this->status->append($msg);
		}
	}
	
	/**
	 * Set (success) message 
	 *
	 * @param Message|string $msg Message instance or message
	 */
	public function message($msg) {
		if ($msg instanceof Message) {
			$this->status = $msg;
		}
		else {
			$this->status = new Message(strval($msg));
		}
	}			

	/**
	 * Returns true, if page call was successfull, false otherwise (CONTROLLER_NOTFOUND et al) 
	 *
	 * @return bool
	 */
	public function successful() {
		return empty($this->status_code) || $this->status_code == CONTROLLER_OK;
	} 
}

