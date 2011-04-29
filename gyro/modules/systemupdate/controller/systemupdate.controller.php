<?php
/**
 * Controller for system update calls
 * 
 * This controller defines only one route: systemupdate. It an be invokes
 * through the command line using the Console module or through the web browser.
 * In later case you need to define SYSTEMUPDATE_PWD, a password the user has to
 * provide to execute the update
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class SystemupdateController extends ControllerBase {
	/**
	 * Returns array of IRoute this controller takes responsibility
	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('https://systemupdate', $this, 'systemupdate_update', new NoCacheCacheManager()),
		);
	}
		
	/**
	 * Update System
	 *
	 * @param PageData $page_data
	 */
	public function action_systemupdate_update(PageData $page_data) {
		if (class_exists('Console') && Console::is_console_request()) {
			$this->do_update_console($page_data);	
		}
		else if (defined('SYSTEMUPDATE_PWD')) {
			Load::tools('formhandler');
			$formhandler = new FormHandler('frmsystemupdate');
			if ($page_data->has_post_data()) {
				// Execute Update
				$this->do_update_form($formhandler, $page_data);	  
			}
			else {
				$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::systemupdate/auth', $page_data);
				$formhandler->prepare_view($view);
				$view->render();
			}
		}
		else {
			return CONTROLLER_NOT_FOUND;
		}
	}
	
	/**
	 * Actually do the updates
	 *
	 * @param FormHandler $formhandler
	 * @param PageData $pagedata
	 */
	protected function do_update_form(FormHandler $formhandler, PageData $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			if ($page_data->get_post()->get_item('a') == SYSTEMUPDATE_PWD) {
				$logs = $this->execute_updates();
		
				$view = ViewFactory::create_view(IViewFactory::CONTENT, 'systemupdate/log', $page_data);
				$view->assign('logs', $logs);
				$view->render();
				return;	
			}
			else {
				$err->append(tr('Sorry, try again', 'systemupdate'));
			}			
		}
		$formhandler->finish($err);
	}
	
	/**
	 * Actually do the updates, invoked from commandline
	 */
	protected function do_update_console($page_data) {
		$logs = $this->execute_updates();
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'systemupdate/log_console', $page_data);
		$view->assign('logs', $logs);
		$view->render();
	}
	
	/**
	 * Execute updates 
	 *
	 * @return array Array of log entries
	 */
	protected function execute_updates() {
		Load::components('systemupdateexecutor');
		$executor = new SystemUpdateExecutor();
		return $executor->execute_updates();
	}
}
