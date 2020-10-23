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
 	public function action_confirm(PageData $page_data, $id, $code) {
		Load::models('confirmations');

		$page_data->in_history = false;
		$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
		$page_data->head->title = tr('Confirmation', 'confirmations');

 		$handler = Confirmations::create_confirmation_handler($id, $code);
 		switch (Config::get_value(ConfigConfirmations::ACTION_INVOCATION)) {
			case ConfigConfirmations::ACTION_DIRECT:
				return $this->handle_confirm_direct($page_data, $handler);

			default:
				return $this->handle_confirm_submit($page_data, $id, $code, $handler);
		}
	}

	/**
	 * Builds and processes the activation page
	 */
	private function handle_confirm_direct(PageData $page_data, IConfirmationHandler $handler) {
		$err = $handler->confirm();
		if ($err->is_error()) {
			$page_data->error($err);
		} else {
			$page_data->message($err);
		}
	}

	/**
	 * Builds a submit page and on POST handles confirmation
	 */
	private function handle_confirm_submit(PageData $page_data, $id, $code, IConfirmationHandler $handler) {
		Load::tools('formhandler');
		$form_handler = new FormHandler('frmconfirm');

		if ($page_data->has_post_data()) {
			$this->do_handle_confirm_submit($handler, $form_handler);
		}

		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'confirmations/submit', $page_data);
		$form_handler->prepare_view($view);
		$view->assign('handler', $handler);
		$view->render();
	}

	/**
	 * Processes confirmation after POST
	 */
	private function do_handle_confirm_submit(IConfirmationHandler $handler, FormHandler $form_handler) {
		$err = $form_handler->validate();
		if ($err->is_ok()) {
			$err->merge($handler->confirm());
		}
		$form_handler->finish($err);
	}


}
