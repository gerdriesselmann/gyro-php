<?php
/**
 * Print a breadcrumb
 * 
 * The breadcrumb is passed an array that may contain different types of content, 
 * namely:
 * 
 * @li a string, which is printed as is (that is: it is not escaped, so you can pass HTML)
 * @li a data object which is translated to the path to the VIEW action. The object must
 *     implement the ISelfDescribing interface. If it implements the IHierarchic interface, too,
 *     its parent will be added to the crumb, also (and its parent's parent, and so on)
 * @li the name of an action as key and a data object as value. This is treated like the object above 
 *     except the VIEW action is replaced by the given one
 *     
 * As usual, you may substitute the array by a single element.
 *     
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetBreadcrumb implements IWidget {
	/**
	 * Link Last Element. If not set (default), the last element will be unlinked
	 */
	const LINK_LAST = 128;
	const USE_PREFIX = 256;
	const BLOCK = 512;
	/** @deprecated */
	const UNLINK_LAST = 1024;
	 
	public $source;
	public $action = 'view';
	public $action_params = null;
	public $prefix = '&gt; ';
	public $glue = ' &gt; ';
	public $url_home; 
	
	public static function output($source, $policy = self::NONE) {
		$w = new WidgetBreadcrumb($source);
		return $w->render($policy);
	}

	public function __construct($source) {
		$this->url_home = Config::get_value(Config::URL_BASEDIR);
		$this->source = $source;
	}
	
	public function render($policy = self::NONE) {
		$src = $this->to_array($this->source);
		$crumb = array();
		$unlink_this = !Common::flag_is_set($policy, self::LINK_LAST);
		foreach($src as $item) {
			$link = ($unlink_this) ? preg_replace('|<a.*?>(.*?)</a>|', '$1', $item) : $item;
			array_unshift($crumb, $link);
			$unlink_this = false;
		}
		$home = html::a(Config::get_value(Config::TITLE), $this->url_home, tr('Go to home page', 'app'));
		array_unshift($crumb, $home);
	
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'core::widgets/breadcrumb');
		$view->assign('breadcrumb_prefix', Common::flag_is_set($policy, self::USE_PREFIX) ? $this->prefix : '');
		$view->assign('breadcrumb_glue', $this->glue);
		$view->assign('breadcrumb_items', $crumb);
		
		return $view->render();
	}
	
	/**
	 * Transform given source into an array. The array has the last item in breadcrumb as first element
	 *
	 * @param mixed $source
	 * @return array
	 */
	protected function to_array($item, $key = false) {
		$arr_ret = array();
		if ($item instanceof IHierarchic) {
			$arr_ret[] = $this->instance2string($item, $key);
			$arr_ret = array_merge($arr_ret, $this->to_array($item->get_parent(), $key));
		}
		else if ($item instanceof ISelfDescribing) {
			$arr_ret[] = $this->instance2string($item, $key);
		}
		else if (is_array($item)) {
			foreach($item as $subkey => $subitem) {
				$arr_ret = array_merge($this->to_array($subitem, $subkey), $arr_ret);			
			}
		}
		else if (!empty($item)) {
			$arr_ret[] = @strval($item);
		}
		return $arr_ret;	
	}
	
	/**
	 * Turn a data obejct into a (link) string 
	 */
	protected function instance2string($instance, $action) {
		if (empty($action) || is_numeric($action)) {
			$action = $this->action;
		}
		return WidgetActionLink::output($instance, $action, $instance);
	}
}