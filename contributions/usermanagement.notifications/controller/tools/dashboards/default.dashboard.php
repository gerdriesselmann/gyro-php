<?php
/**
 * Default dashboard for all users
 * 
 * @author Gerd Riesselmann
 * @ingroup Notifications
 */
class DefaultDashboard extends DashboardBase {
	/**
	 * Return template file name for dashboard
	 *
	 * @return string
	 */
	protected function get_template_file_name() {
		return 'users/dashboards/default';
	}

	/**
	 * Return array of entries for user menu
	 */
	public function get_user_menu_entries() {
		$user = $this->get_user();
		$li = array(
			WidgetActionLink::output(tr('Edit your account', 'users'), 'edit_self', $user),
			WidgetActionLink::output(tr('Notification Settings', 'notifications'), 'notifications_settings'),
			WidgetActionLink::output(tr('Your Notifications', 'notifications'), 'users_notifications'),
		);
		return $li;
	}	
	
}