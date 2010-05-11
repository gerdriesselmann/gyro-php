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
			new ExactMatchRoute('https://user/notifications/', $this, 'users_notifications', new AccessRenderDecorator()),
			new ExactMatchRoute('https://ajax/notifications/toggle', $this, 'notifications_ajax_toggle', new AccessRenderDecorator()),
			new ExactMatchRoute('https://user/notifications/settings/', $this, 'notifications_settings', new AccessRenderDecorator()),
			new ParameterizedRoute('https://notifications/feeds/{id_user:ui>}/{feed_token:s:40}', $this, 'notifications_feed', new NoCacheCacheManager()),
			new ParameterizedRoute('https://notifications/{id:ui>}/', $this, 'notifications_view', new AccessRenderDecorator()),
			// Command Line 
			new ExactMatchRoute('notifications/digest', $this, 'notifications_digest', new ConsoleOnlyRenderDecorator())
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
	 * Show given notification
	 */
	public function action_notifications_view(PageData $page_data, $id) {
		Load::models('notifications');
		$n = Notifications::get($id);
		if ($n === false || !Users::is_current($n->get_user())) {
			return self::ACCESS_DENIED;
		}
		
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'notifications/view', $page_data);
		$view->assign('notification', $n);
		$view->render();
	}
	
	/**
	 * Change notifcation settings
	 */
	public function action_notifications_settings(PageData $page_data) {
		Load::models('notificationssettings');
		Load::tools('formhandler');
		$formhandler = new FormHandler('notificationssettings');
		
		$user = Users::get_current_user(); 
		$settings = NotificationsSettings::get_for_user($user->id); // can be false
		
		if ($page_data->has_post_data()) {
			$this->do_notifications_settings($page_data, $formhandler, $settings);
		}
		
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'notifications/settings', $page_data);
		$view->assign('sources', NotificationsSettings::collect_sources($user));
		$view->assign('settings', $settings);
		
		if (empty($settings)) {
			$settings = new DAONotificationssettings();
			$settings->set_default_values();
		}
		$formhandler->prepare_view($view, $settings);
		
		$view->render(); 
	}
	
	/**
	 * Save settings
	 */
	protected function do_notifications_settings(PageData $page_data, FormHandler $formhandler, $settings) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			$update = ($settings != false);
			$data = $page_data->get_post()->get_array();
			$data['id_user'] = Users::get_current_user()->id;
			if (!$update) {
				$settings = new DAONotificationssettings();
			}
			$data = $settings->unset_internals($data);
			
			$cmd = $update 
				? CommandsFactory::create_command($settings, 'update', $data) 
				: CommandsFactory::create_command('notificationssettings', 'create', $data);
			$err->merge($cmd->execute());			
		}
		$formhandler->finish($err, tr('Your settings have been saved', 'notifications'));
	}
	
	/**
	 * Notifications feed
	 */
	public function action_notifications_feed(PageData $page_data, $id_user, $feed_token) {
		Load::models('notificationssettings');
		$settings = NotificationsSettings::get_for_user($id_user);
		if ($settings === false || !$settings->feed_enable || $settings->feed_token != $feed_token) {
			return self::NOT_FOUND;
		}

		// Find notifications
		$dao = NotificationsSettings::create_feed_adapter($settings);
		
		$view = ViewFactory::create_view(ViewFactoryMime::MIME, 'notifications/feed', $page_data);
		$view->assign('notifications', $dao->execute());
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
	
	/**
	 * Send a mail digest
	 */
	public function action_notifications_digest(PageData $page_data) {
		Load::models('notificationssettings');
		$err = new Status();
		
		$possible_digests = NotificationsSettings::create_possible_digest_adapter();
		$possible_digests->find();
		while($possible_digests->fetch()) {
			$cmd = CommandsFactory::create_command(clone($possible_digests), 'digest', false);
			$err->merge($cmd->execute());
		}
		
		$page_data->status = $err;
	}
}