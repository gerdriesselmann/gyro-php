<?php
require_once dirname(__FILE__) . '/renderdecoratorbase.cls.php';

/**
 * A render decorator that checks for validation tokens
 * 
 * Used with Commands Processing Routes
 * 
 * @author Gerd Riesselmann
 * @ingroup Controller 
 */
class CommandsRouteRenderDecorator extends RenderDecoratorBase {
	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		Load::tools('formhandler');
		$formhandler = new FormHandler('process_commands');
		$err = $formhandler->validate($_GET);
		if ($err->is_error()) {
			$formhandler->error($err);
			exit;
		}
		
		parent::initialize($page_data);
	}	
}