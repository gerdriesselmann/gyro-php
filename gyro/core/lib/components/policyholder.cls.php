<?php
/**
 * Class holding a policy (bitflags)
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class PolicyHolder implements IPolicyHolder {
	protected $policy = self::NONE;

	/**
	 * Constructor
	 *
	 * @param int $policy
	 */
	public function __construct($policy = self::NONE) {
		$this->policy = $policy;
	}
	
	/**
	 * Return policy
	 *
	 * @return int
	 */
	public function get_policy() {
		return $this->policy;
	}
	
	/**
	 * Set policy
	 *
	 * @param int $policy
	 */
	public function set_policy($policy) {
		$this->policy = $policy;
	}
	
	/**
	 * Returns true, if client has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy) {
		return Common::flag_is_set($this->get_policy(), $policy);
	}
}
