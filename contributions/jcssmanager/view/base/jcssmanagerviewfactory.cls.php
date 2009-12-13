<?php
/**
 * Overload View Factory to cope with 
 *
 */
class ViewFactoryJCSSManager extends ViewFactoryBase {
	const PAGE_CONSOLE = 'PAGE_CONSOLE';
	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		if ($type == self::PAGE_CONSOLE) {
			return new ConsolePageView($params, $template_name); 
		}
		return parent::create_view($type, $template_name, $params);
	}
}
