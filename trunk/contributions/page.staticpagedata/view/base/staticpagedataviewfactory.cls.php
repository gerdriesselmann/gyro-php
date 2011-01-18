<?php
/**
 * Overload View Factory to catch recent content view's PageData
 * 
 * @author Gerd Riesselmann
 * @ingroup StaticPageData
 */
class ViewFactoryStaticPageData extends ViewFactoryBase {
	
	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		if ($type == self::CONTENT) {
			StaticPageData::set_data($params);	
		}
		return parent::create_view($type, $template_name, $params);
	}
}
