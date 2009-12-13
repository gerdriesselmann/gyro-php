<?php
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
		$li = array();
		$li[] = html::a(
			tr('Edit your account', 'users'), 
			ActionMapper::get_url('edit_self', $user),
			''
		);
		return $li;
	}	
	
}