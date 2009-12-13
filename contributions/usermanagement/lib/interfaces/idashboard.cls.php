<?php
/**
 * Created on 15.12.2006
 *
 * @author Gerd Riesselmann
 */
 
/**
 * Interface for Dashboard implementations
 */
interface IDashboard {
	/**
	 * Returns the title of the dashboard
	 */
	public function get_title();
	
	/**
	 * Returns description of dashboard
	 */
	public function get_description();
	
	/**
	 * Render a section on the dashboard
	 */
	public function get_content($page_data);
	
	/**
	 * Return array of entries for user menu
	 */
	public function get_user_menu_entries();
}
?>