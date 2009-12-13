<?php
/**
 * Overload View Factory to create AjaxView
 * 
 * @author Gerd Riesselmann
 * @ingroup Ajax
 */
class ViewFactoryAjax extends ViewFactoryBase {
	const AJAX = 'AJAX';
	
	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		if ($type == self::AJAX) {
			return new AjaxView($params);	
		}
		return parent::create_view($type, $template_name, $params);
	}
}
?>