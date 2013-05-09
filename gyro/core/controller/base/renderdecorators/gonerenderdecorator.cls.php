<?php
/**
 * Renders a Gone (410) result
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class GoneRenderDecorator extends RenderDecoratorBase {
	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$page_data->in_history = false;
		$page_data->set_cache_manager(new ConstantCacheManager('error-410'));
		$page_data->status_code = CONTROLLER_NOT_FOUND;
		$page_data->status_code_http = 410; // Gone
	}
	
	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data) {
		$page_data->head->title = tr('Page not found', 'core');		
	}	
}
?>