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
			new ExactMatchRoute('https://user/notifications/settings/', $this, 'notifications_settings', new AccessRenderDecorator()),
			new ParameterizedRoute('https://notifications/feeds/{id_user:ui>}/{feed_token:s:40}', $this, 'notifications_feed', new NoCacheCacheManager()),
			new ParameterizedRoute('https://notifications/{id:ui>}/', $this, 'notifications_view', new AccessRenderDecorator()),
			new NotificationsExcludeRoute('https://notifications/exclude/{source:s}/{source_id:ui>}/{token:s}/', $this, 'notifications_exclude', new AccessRenderDecorator()),
			// Ajax
			new ExactMatchRoute('https://ajax/notifications/toggle', $this, 'notifications_ajax_toggle', new AccessRenderDecorator()),
			// Clicktracking
			new ParameterizedRoute('https://notifications/{id:ui>}/click/', $this, 'notifications_clicktrack'),
			// Command Line 
			new ExactMatchRoute('notifications/digest', $this, 'notifications_digest', new ConsoleOnlyRenderDecorator()),
			// COmmands
			new CommandsRoute('https://process_commands/notifications/markallasread', $this, 'markallasread_cmd_handler', new AccessRenderDecorator()),
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
		
		$src = $page_data->get_get()->get_item('src', false);
		if ($src) {
			$src = strtoupper($src);
			if (key_exists($src, Notifications::get_read_sources())) {
				$cmd = CommandsFactory::create_command($n, 'markread', array('read_through' => $src, 'read_action' => 'click'));
				$cmd->execute(); 
			}
		}
		
		$page_data->head->title = $n->get_title();
		$page_data->breadcrumb = WidgetBreadcrumb::output(array(
			WidgetActionLink::output(tr('Your Notifications', 'notifications'), 'users_notifications'),
			$n
		));
		
		
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'notifications/view', $page_data);
		$view->assign('notification', $n);
		$view->render();
	}
	
	/**
	 * Track links inside notifications messages
	 */
	public function action_notifications_clicktrack(PageData $page_data, $id) {
		Load::models('notifications');
		$n = Notifications::get($id);
		if ($n === false) {
			return self::NOT_FOUND;
		}
		
		$url = $page_data->get_get()->get_item('url', '');
		$src = $page_data->get_get()->get_item('src', false);
		$token = $page_data->get_get()->get_item('token', '');
		if ($token != $n->click_track_fingerprint($src, $url)) {
			return self::NOT_FOUND;
		}
		
		$cmd = CommandsFactory::create_command($n, 'markread', array('read_through' => $src, 'read_action' => 'click'));
		$cmd->execute(); 
		
		Url::create_with_fallback_host($url, Config::get_value(Config::URL_DOMAIN))->redirect(Url::TEMPORARY);
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
		$page_data->in_history = false;
		Load::models('notificationssettings');
		$settings = NotificationsSettings::get_for_user($id_user);
		if ($settings === false || !$settings->is_feed_enabled() || $settings->feed_token != $feed_token) {
			return self::NOT_FOUND;
		}

		// Find notifications
		$dao = NotificationsSettings::create_feed_adapter($settings);
		$nots = array();
		$dao->find();
		while($dao->fetch()) {
			if ($settings->should_notification_be_processed($dao, NotificationsSettings::TYPE_FEED)) {
				$n = clone($dao);
				$nots[] = $n;
				$n->add_sent_as(Notifications::DELIVER_FEED);
				$cmd = CommandsFactory::create_command($n, 'update', array());
				$cmd->execute();
			}
		}
		
		$view = ViewFactory::create_view(ViewFactoryMime::MIME, 'notifications/feed', $page_data);
		$view->assign('notifications', $nots);
		$view->render();
	}
	
	/**
	 * Exclude items
	 */
	public function action_notifications_exclude(PageData $page_data, $source, $source_id, $token) {
		$data = array('source' => $source, 'source_id' => $source_id);
		$user = Users::get_current_user();
		if ($token != $user->create_token('exclude', $data)) {
			return self::NOT_FOUND;
		}
		
		$data['id_user'] = $user->id;
		$cmd = CommandsFactory::create_command('notificationsexceptions', 'create', $data);
		$err = $cmd->execute();
		if ($err->is_ok()) {
			$err = new Message(tr('Notifications have been disabled for the given item', 'notifications'));
		}
		History::go_to(0, $err, ActionMapper::get_url('users_notifications'));
	}
	
	/**
	 * Toggle a message state 
	 */
	public function action_notifications_ajax_toggle(PageData $page_data) {
		$page_data->in_history = false;
		$page_data->page_template = 'emptypage';
		$id = $page_data->get_post()->get_item('id', false);
		$notification = DB::get_item('notifications', 'id', $id);
		if (!AccessControl::is_allowed('status', $notification)) {
			return self::ACCESS_DENIED;
		}
		
		$new_status = ($notification->get_status() != Notifications::STATUS_NEW) ? Notifications::STATUS_NEW : Notifications::STATUS_READ;
		$cmd = CommandsFactory::create_command($notification, 'status', $new_status);
		$err = $cmd->execute();
		if ($err->is_ok()) {
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'notifications/inc/item', $page_data);
			$view->assign('notification', $notification);
			$page_data->content = $view->render();
		}
		else {
			return self::INTERNAL_ERROR;
		}
	}
	
	/**
	 * Send a mail digest
	 */
	public function action_notifications_digest(PageData $page_data) {
		$cmd = CommandsFactory::create_command('notifications', 'digest', false);
		$page_data->status = $cmd->execute();
	}
	
	public function action_markallasread_cmd_handler(PageData $page_data) {
		$cmd = CommandsFactory::create_command('notifications', 'markallasread', false);
		if (!$cmd->can_execute(false)) {
			return CONTROLLER_ACCESS_DENIED;
		}
		$err = $cmd->execute();
		if ($err->is_ok()) {
			$err = new Message(tr('All notifications have been marked as read', 'notifications'));
		}
		History::go_to(0, $err);
		exit; 
	}
}