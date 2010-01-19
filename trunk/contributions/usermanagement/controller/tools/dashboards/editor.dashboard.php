<?php
/**
 * Default dashboard for editor
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class EditorDashboard extends DashboardBase {
	/**
	 * Returns the title of the dashboard
	 */
	public function get_title() {
		return tr('Content Management', 'users');
	}
	
	/**
	 * Return template file name for dashboard
	 *
	 * @return string
	 */
	protected function get_template_file_name() {
		return 'users/dashboards/editor';
	}
}