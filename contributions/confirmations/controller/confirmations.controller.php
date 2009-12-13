<?php
/**
 * Confirmation controller
 * 
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
class ConfirmationsController extends ControllerBase {
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */
 	public function get_routes() {
 		return array(
 			new ParameterizedRoute('confirm/{id:ui>}/{code:s}', $this, 'confirm', new NoCacheCacheManager()),
 		);
 	}

	/**
	 * Builds and processes the activation page
	 */
 	public function action_confirm($page_data, $id, $code) {
		Load::models('confirmations');
 		$handler = Confirmations::create_confirmation_handler($id, $code);
		$err = $handler->confirm();
		if ($err->is_error()) {
			$page_data->error($err);
		}
		else {
			$page_data->message($err);
		}
		$page_data->in_history = false;
		$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
		$page_data->head->title = tr('Confirmation', 'confirmations');
	}
 	
}
