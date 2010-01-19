<?php
/**
 * Dashboard base class
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DashboardBase implements IDashboard {
	/**
	 * USer
	 *
	 * @var DAOUsers
	 */
	protected $user = null;
	
	public function __construct($user) {
		$this->user = $user;
	}
	
	/**
	 * Returns user
	 *
	 * @return DAOUsers
	 */
	protected function get_user() {
		return $this->user;
	}
	
	/**
	 * Returns the title of the dashboard
	 */
	public function get_title() {
		return tr('Your links', 'users');
	}
	
	/**
	 * Returns description of dashboard
	 */
	public function get_description() {
		return '';
	}
	
	/**
	 * Create a view to render
	 */
	public function get_content($page_data) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, $this->get_template_file_name(), $page_data);
		$this->prepare_view($view, $page_data);		
		return trim($view->render());		
	}
	
	/**
	 * Return template file name for dashboard
	 *
	 * @return string
	 */
	protected function get_template_file_name() {
		throw new Exception('get_template_file_name() not implemented on ' . get_class($this));
	}
	
	/**
	 * Allwos subclasses to prepare the view
	 *
	 * @param IView $view
	 * @param PageData $page_data
	 */
	protected function prepare_view($view, $page_data) {
		$view->assign('dashboard', $this);
		$view->assign('user', $this->get_user());
	}
	
	/**
	 * Return array of entries for user menu
	 */
	public function get_user_menu_entries() {
		return array();
	}
}