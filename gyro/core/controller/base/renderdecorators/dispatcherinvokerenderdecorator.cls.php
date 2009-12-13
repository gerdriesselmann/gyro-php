<?php
require_once dirname(__FILE__) . '/renderdecoratorbase.cls.php';

/**
 * Renders result of a controller action
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class DispatcherInvokeRenderDecorator extends RenderDecoratorBase {
	/**
	 * Dispatcher to invoke
	 *
	 * @var IDispatcher
	 */
	protected $dispatcher;
	
	/**
	 * Constructor
	 *
	 * @param IDispatcher $dispatcher The dispatcher to invoke
	 */
	public function __construct($dispatcher) {
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * Invokes assigned controller   
	 * 
	 * @param object Page data object
	 * @return void 
 	 */
	public function render_content($page_data) {
		$this->dispatcher->invoke($page_data); 
	}

	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return mixed
	 */
	public function render_page($page_data, $content_render_decorator, $policy = IView::NONE) {
		$view = ViewFactory::create_view(IViewFactory::PAGE, $page_data->page_template, $page_data);
		if ($view->is_cached() == false) {
			$page_data->router->preprocess($page_data);
			$content_render_decorator->render_content($page_data);
			$page_data->router->postprocess($page_data);
		}
		return $view->render($policy);	
	}
}