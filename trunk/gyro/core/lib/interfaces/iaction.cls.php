<?php
/**
 * An action
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IAction {
	/**
	 * Returns title of action.
	 * 
	 * @return string
	 */
	public function get_name();
	
	/**
	 * Returns a description of this action
	 * 
	 * @return string
	 */
	public function get_description(); 	
	
	/**
	 * Returns the object this actionworks upon
	 *
	 * @return mixed
	 */
	public function get_instance();
	
}
