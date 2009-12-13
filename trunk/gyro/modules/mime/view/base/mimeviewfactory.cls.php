<?php
/**
 * Overload View Factory to create Mime View
 * 
 * @author Gerd Riesselmann
 * @ingroup Mime
 */
class ViewFactoryMime extends ViewFactoryBase {
	const MIME = 'MIME';
	
	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		if ($type == self::MIME) {
			return new MimeView($template_name, $params); 
		}
		return parent::create_view($type, $template_name, $params);
	}
}
