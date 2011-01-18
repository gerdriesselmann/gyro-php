<?php
/**
 * Renders result as Ajax Response
 * 
 * @author Gerd Riesselmann
 * @ingroup Ajax
 */
class AjaxRenderDecorator extends RenderDecoratorBase {
	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return mixed
	 */
	public function render_page($page_data, $content_render_decorator, $policy = IView::NONE) {
		$view = ViewFactory::create_view(ViewFactoryAjax::AJAX, '', $page_data);
		$page_data->router->preprocess($page_data);
		// Expected data to be set as $page_data->ajax_data
		$page_data->ajax_data = array();
		$content_render_decorator->render_content($page_data);
		$page_data->router->postprocess($page_data);
		$page_data->in_history = false;
		return $view->render();	
	}
}
