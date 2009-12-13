<?php
class JCSSManagerController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('jcssmanager/compress', $this, 'jcssmanager_compress', new ConsoleOnlyRenderDecorator()),
			 
		);
	}	
	
	public function action_jcssmanager_compress(PageData $page_data) {
		$err = JCSSManager::collect_and_compress();
		$page_data->status = $err;
	}
}