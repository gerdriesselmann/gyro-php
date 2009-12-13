<?php
/**
 * Renders result of a controller action for console output
 * 
 * This render decorator gets autmatically set, if you invoke 
 * an action through the console.
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class ConsoleRenderDecorator extends DispatcherInvokeRenderDecorator {
	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return mixed
	 */
	public function render_page($page_data, $content_render_decorator, $policy = IView::NONE) {
		$view = ViewFactory::create_view(ViewFactoryConsole::PAGE_CONSOLE, $page_data->page_template, $page_data);
		$page_data->router->preprocess($page_data);
		$content_render_decorator->render_content($page_data);
		$page_data->router->postprocess($page_data);
		return $view->render($policy);	
	}
}