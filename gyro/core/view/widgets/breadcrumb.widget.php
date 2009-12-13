<?php
/**
 * Print a breadcrumb
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetBreadcrumb implements IWidget {
	const LINK_LAST = 128;
	const USE_PREFIX = 256;
	const BLOCK = 512;
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
		$link_this = Common::flag_is_set($policy, self::LINK_LAST);
		$unlink_this = Common::flag_is_set($policy, self::UNLINK_LAST);
		foreach($src as $item) {
			$text = '';
			$descr = '';
			if ($item instanceof ISelfDescribing) {
				$text = String::escape($item->get_title());
				$descr = $item->get_description();
			}
			else {
				$text = @strval($item);
				$link_this = false;
			}
			$link = ($link_this) ? html::a($text, ActionMapper::get_path($this->action, $item), $descr) : $text;
			$link = ($unlink_this) ? preg_replace('|<a.*?>(.*?)</a>|', '$1', $link) : $link;
			array_unshift($crumb, $link);
			$link_this = true;
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
	protected function to_array($source) {
		$arr_ret = array();
		if ($source instanceof IHierarchic) {
			$arr_ret[] = $source;
			$arr_ret = array_merge($arr_ret, $this->to_array($source->get_parent()));
		}
		elseif (is_array($source)) {
			foreach($source as $item) {
				$arr_ret = array_merge($this->to_array($item), $arr_ret);			
			}
		}
		else if (!empty($source)) {
			$arr_ret[] = $source;
		}
		return $arr_ret;	
	}
}