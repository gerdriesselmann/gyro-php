<?php
Load::directories('view/widgets');

/**
 * Static view factory
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class ViewFactory {
	/**
	 * Implementation to delegate view creation to
	 *
	 * @var IViewFactory
	 */
	private static $implementation = null;
	
	/**
	 * Change Implementation
	 *
	 * @param $implementation 
	 *   The new implementation
	 * @param $keep_old 
	 *   If set to TRUE, an existing implementation will not get replaced.
	 *   Don't mix this with a call to set_old_implementation() of the new factory implementation,
	 *   since this will always be done.   
	 */
	public static function set_implementation(IViewFactory $implementation, $keep_old = false) {
		// keep old, if told so 
		if (!empty(self::$implementation)) {
			if ($keep_old) {
			 	return;
			}
			$implementation->set_old_implementation(self::$implementation); 
		}
		self::$implementation = $implementation;
	}
	
	/**
	 * Create a suitable view
	 *
	 * @param $type The type of view to create as a string, e.g. "page", or "content", or "XML" ...  
	 * @param $template_name Name of the template
	 * @param $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public static function create_view($type, $template_name, $params= false) {
		if (empty(self::$implementation)) {
			self::set_implementation(new ViewFactoryBase());
		}
		return self::$implementation->create_view($type, $template_name, $params);
	}
}
