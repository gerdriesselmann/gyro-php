<?php
/**
 * Translates URLs into filter 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IFilterAdapter {
	/**
	 * Return key for given group
	 * 
	 * @return string 
	 */
	public function get_current_key($group_id, $default = '');

	/**
	 * Build URL for filter
	 */
	public function get_filter_link($filter, $group_id);	
}