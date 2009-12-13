<?php
/**
 * Translates URL into free text filter
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IFilterTextAdapter {
	/**
	 * Return current value to be filtered after
	 * 
	 * @return string
	 */
	public function get_value();

	/**
	 * Name of param holding value
	 * 
	 * @return string
	 */
	public function get_param();
	
	/**
	 * Name of param indicating reset
	 * 
	 * @return string
	 */
	public function get_reset_param();
}