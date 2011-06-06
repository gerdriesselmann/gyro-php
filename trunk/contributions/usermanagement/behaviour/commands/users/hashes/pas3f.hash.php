<?php
require_once dirname(__FILE__) . '/pas3p.hash.php';

/**
 * Calculates a hash using PHPPass 0.3 in full mode
 * 
 * @since 0.6
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Pas3fHash extends Pas3pHash {
	/**
	 * Return preconfigured instance of PasswordHash
	 * 
	 * @return PasswordHash03
	 */
	protected function create_pass3_instance() {
		return new PasswordHash03(8, FALSE);
	}	
}