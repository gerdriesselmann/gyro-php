<?php
require_once dirname(__FILE__) . '/pas2p.hash.php';

/**
 * Calculates a hash using PHPPass 0.2 in full mode
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Pas2fHash extends Pas2pHash {
	/**
	 * Return preconfigured instance of PasswordHash
	 * 
	 * @return PasswordHash02
	 */
	protected function create_pass2_instance() {
		return new PasswordHash02(8, FALSE);
	}	
}