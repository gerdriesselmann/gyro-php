<?php
/**
 * Redirect to given target path (not Url!)  
 *
 * This class understands backreferences {$0}, {$1}, {$2} etc. which are segments of the orginal path
 * 
 * $0 references the full source path, while $1 to $n reference to nth element of the source path
 *  
 * Example:
 * Given URL /old/url/with/name should be redirected to /new/url/name.
 *  
 * This can be achieved by defining a RedirectRenderDecorator with target path
 *  
 * /new/url/{$4} 
 *  
 * {$4} because name is the 4th elements of the original path. First would be "old", second "url" and so on.
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class RedirectRenderDecorator extends RenderDecoratorBase {
	/**
	 * Url to redirect to
	 *
	 * @var string
	 */
	private $target_path = null;

	/**
	 * Constructor
	 *
	 * @param ICacheManager $cache_manager Desired Cache Manager
	 * @return void
	 */
	public function __construct($target_path) {
		$this->target_path = $target_path;
	}

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$target = $this->target_path;
		$source_path = Url::current()->get_path();
		$full_target = $this->build_redirect_url($target, $source_path);
				
		$url = Url::create($full_target);
		if (!$url->is_valid()) {
			$url = Url::current()->clear_query()->set_path($full_target);
		}
		$url->redirect(Url::PERMANENT);
		exit;
	}
	
	/**
	 * Change URL to point ot new location
	 */
	protected function build_redirect_url($target_path, $source_path) {
		// Replace back references
		// $0
		$target_path = str_replace('{$0}', $source_path, $target_path);
		// $1 to $n
		$i = 1;
		$path_stack = new PathStack($source_path);
		while($elem = $path_stack->shift()) {
			$target_path = str_replace('{$' . $i . '}', $elem, $target_path);	
			$i++;
		}
		// remove not referenced back reference
		$target_path = preg_replace('|\{\$\d\}|', '', $target_path);
		return $target_path;
	}
}