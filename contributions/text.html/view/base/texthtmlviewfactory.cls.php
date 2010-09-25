<?php
/**
 * Overload View Factory to enable rich text editors
 * 
 * @author Gerd Riesselmann
 * @ingroup Html
 */
class ViewFactoryTextHtml extends ViewFactoryBase {
	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		$ret = parent::create_view($type, $template_name, $params);
		if ($type == self::CONTENT) {
			// After creating a content view, enable editors
			// $params is of type PageData
			HtmlText::apply_enabled_editors($params);	
		}
		return $ret;
	}
}
