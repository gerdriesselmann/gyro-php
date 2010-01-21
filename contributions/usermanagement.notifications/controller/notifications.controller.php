<?php
/**
 * There are some routes to handle for notifications
 */
class NotificationsController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('https://user/notifications', $this, 'users_notifications', new AccessRenderDecorator()),
			new ExactMatchRoute('https://ajax/notifications/toggle', $this, 'notifications_ajax_toggle', new AccessRenderDecorator()),
		);	
	}	
	
	/**
	 * Show list of all notifications
	 * 
	 * @param PageData $page_data
	 */
	public function action_users_notifications(PageData $page_data) {
		Load::models('notifications');
		$adapter = Notifications::create_user_adapter(Users::get_current_user()->id);

		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'notifications/my', $page_data);
		Load::tools('pager', 'filter');
		
		$filter = new Filter($page_data, $adapter->get_filters());
		$filter->apply($adapter);
		$filter->prepare_view($view);
		
		$pager = new Pager($page_data, $adapter->count(), Config::get_value(Config::ITEMS_PER_PAGE));
		$pager->apply($adapter);
		$pager->prepare_view($view);
		
		$view->assign('notifications', $adapter->execute());
		$view->render();
	}
	
	/**
	 * Toggle a message state 
	 */
	public function action_notifications_ajax_toggle(PageData $page_data) {
		$page_data->page_template = 'emptypage';
		$id = $page_data->get_post()->get_item('id', false);
		$notification = DB::get_item('notifications', 'id', $id);
		if ($notification === false || !Users::is_current($notification->get_user())) {
			return self::ACCESS_DENIED;
		}
		
		$new_status = ($notification->get_status() != Notifications::STATUS_NEW) ? Notifications::STATUS_NEW : Notifications::STATUS_READ;
		$notification->set_status($new_status);
		$err = $notification->update();
		if ($err->is_ok()) {
			$page_data->content = $new_status;
		}
		else {
			return self::INTERNAL_ERROR;
		}
	}
}