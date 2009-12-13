<?php
/**
 * View Factory extension interface 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IViewFactory {
	const PAGE = 'page';
	const CONTENT = 'content';
	const MESSAGE = 'message';
	const XML = 'xml';

	/**
	 * Create a suitable view
	 *
	 * @param string $type The type of view to create e.g. "page", or "content", or "XML" ...  
	 * @param string $template_name Name of the template
	 * @param mixed $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params);
	
	/**
	 * Set old implementation. Requests not handled should be delegated to this
	 *
	 * @param IViewFactory $implementation
	 */
	public function set_old_implementation(IViewFactory $implementation);
}