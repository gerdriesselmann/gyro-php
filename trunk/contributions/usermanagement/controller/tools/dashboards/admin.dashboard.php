<?php
/**
 * Default dashboard implementation for admins
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class AdminDashboard extends DashboardBase {
	/**
	 * Returns the title of the dashboard
	 */
	public function get_title() {
		return tr('Application Management', 'users');
	}
	
	/**
	 * Return template file name for dashboard
	 *
	 * @return string
	 */
	protected function get_template_file_name() {
		return 'users/dashboards/admin';
	}
}