<?php
/**
 * Catch view events to extend rendering
 *
 */
class UsersViewEventSink implements IEventSink {
	/**
	 * Invoked to handle events
	 */
	public function on_event($name, $params, &$result) {
		switch($name) {
			case 'view_before_render':
				$view = $params['view'];
				$view->assign('current_user', Users::get_current_user());
				$view->assign('is_logged_in', Users::is_logged_in());
				break;
			default:
				break;
		}
	}	
}
