<?php
/**
 * Something with a polciy (bitflags)
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IPolicyHolder {
	const NONE = 0;
	
	/**
	 * Return policy
	 *
	 * @return int
	 */
	public function get_policy();
	
	/**
	 * Set policy
	 *
	 * @param int $policy
	 */
	public function set_policy($policy);
	
	/**
	 * Returns true, if client has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy);
}