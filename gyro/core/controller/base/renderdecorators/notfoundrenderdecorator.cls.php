<?php
/**
 * Renders a not found (404) result 
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class NotFoundRenderDecorator extends RenderDecoratorBase {
	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$page_data->set_cache_manager(new ConstantCacheManager('error-404'));
		$page_data->status_code = CONTROLLER_NOT_FOUND;
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
