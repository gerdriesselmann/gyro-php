<?php
/**
 * Shows latest notifications
 */
class WidgetLatestNotifications implements IWidget {
	/**
	 * Pagedata to hold Head Information
	 *
	 * @var PageData
	 */
	public $page_data;
	/**
	 * User to list notifications for
	 *
	 * @var DAOUsers
	 */
	public $user;
	public $num;

	/**
	 * Output latest notifications
	 *
	 * @param DAOUsers $user
	 * @param int $policy 
	 * @return string
	 */
	public static function output($page_data, $user, $num = 5, $policy = self::NONE) {
		$w = new WidgetLatestNotifications($page_data, $user, $num);
		return $w->render($policy);
	}
	
	public function __construct($page_data, $user, $num) {
		$this->page_data = $page_data;
		$this->user = $user;
		$this->num = $num;
	}
	
	/**
	 * Renders
	 *
	 * @param int $policy
	 * @return string
	 */
	public function render($policy = self::NONE) {
		Load::models('notifications');
		$notifications = Notifications::create_unread_user_adapter($this->user->id);
		$total = $notifications->count();
		$notifications->limit(0, $this->num);

		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/notifications.latest', false);
		$view->assign('notifications', $notifications->execute());
		$view->assign('total', $total);
		$view->assign('page_data', $this->page_data);
		return $view->render();
	}		
}