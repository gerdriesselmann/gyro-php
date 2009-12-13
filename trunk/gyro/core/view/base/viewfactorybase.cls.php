<?php

/**
 * Default implementation of view factory
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class ViewFactoryBase implements IViewFactory {
	/**
	 * Old implementation to delegate to  
	 *
	 * @var IViewFactory
	 */
	private $delegate = null;
	
	/**
	 * Create a suitable view
	 *
	 * @param $type The type of view to create as string e.g. "page", or "content", or "XML" ...  
	 * @param $template_name Name of the template
	 * @param $params Params to pass to view, may depend on type
	 * @return IView
	 */
	public function create_view($type, $template_name, $params) {
		if ($this->delegate) {
			return $this->delegate->create_view($type, $template_name, $params);
		}
		else {
			switch ($type) {
				case IViewFactory::PAGE:
					require_once(dirname(__FILE__) . '/pageviewbase.cls.php');
					return new PageViewBase($params, $template_name);
				case IViewFactory::CONTENT:
					require_once(dirname(__FILE__) . '/contentviewbase.cls.php');
					return new ContentViewBase($template_name, $params);
				case IViewFactory::MESSAGE:
					require_once(dirname(__FILE__) . '/messageviewbase.cls.php');
					return new MessageViewBase($template_name);
				case IViewFactory::XML:
					require_once(dirname(__FILE__) . '/xmlviewbase.cls.php');
					return new XmlViewBase($template_name, $params);
			}
		}
		throw new Exception(tr('Unkown view type %t','core', array('%t' => $type)));
	}

	/**
	 * Set old implementation. Requests not handled should be delegated to this
	 *
	 * @param $implementation Another IViewFactory
	 */
	public function set_old_implementation(IViewFactory $implementation) {
		$this->delegate = $implementation;
	}
}
