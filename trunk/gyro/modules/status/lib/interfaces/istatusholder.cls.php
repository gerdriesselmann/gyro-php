<?php
/**
 * Interface for any class having status
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
interface IStatusHolder {
	/**
	 * Set status
	 *
	 * @param string $status
	 */
	public function set_status($status);
	
	/**
	 * Returns status
	 * 
	 * @return string
	 */
	public function get_status();
	
	/**
	 * Returns true, if status is active
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * Returns true, if status is unconfirmed
	 *
	 * @return bool
	 */
	public function is_unconfirmed();
	
	/**
	 * Returns true, if status is deleted
	 *
	 * @return bool
	 */
	public function is_deleted();
	
	/**
	 * Returns true, if status is disabled
	 *
	 * @return bool
	 */
	public function is_disabled();	
}