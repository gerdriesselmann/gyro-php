<?php
/**
 * Defines a view route for binaries
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class BinariesBaseController extends ControllerBase {
	
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */
 	public function get_routes() {
 		$ret = array(
 			'view' => new ParameterizedRoute('binaries/{id:ui>}', $this, 'binaries_view', $this->get_route_decorators()),
 		);
 		return $ret;
 	}

 	/**
 	 * Return Decorators for binaries routes
 	 *
 	 * @return mixed
 	 */
 	protected function get_route_decorators() {
 		return array(new BinariesCacheManager());
 	}
 	
	/**
	 * Activates includes before action to reduce cache memory 
	 */ 
	public function before_action() {
		Load::models('binaries');	 	
	}
	 
 	/** 
 	 * Show binary
 	 * 
 	 * @param $page_data PageData
 	 * @param $id ID of binary
 	 */
 	public function action_binaries_view($page_data, $id) {
 		$binary = Binaries::get($id);
		if (empty($binary)) {
			return CONTROLLER_NOT_FOUND; 	
		}
		
		$view = ViewFactory::create_view(ViewFactoryMime::MIME, 'binaries/view', $page_data);
		$view->assign(MimeView::MIMETYPE, $binary->mimetype);
		$view->assign('binary', $binary);
		$view->render();
 	}
}
