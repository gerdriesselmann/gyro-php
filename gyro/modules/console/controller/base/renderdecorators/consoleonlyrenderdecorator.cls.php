<?php
/**
 * Return 403 if not invoked from console, effectively forcing users to 
 * use the console 
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class ConsoleOnlyRenderDecorator extends RenderDecoratorBase {
	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data) {
		if (!class_exists('Console') || !Console::is_console_request()) {
			$page_data->status_code = CONTROLLER_ACCESS_DENIED;
		}
		else {
			parent::render_content($page_data);
		}
	}
}