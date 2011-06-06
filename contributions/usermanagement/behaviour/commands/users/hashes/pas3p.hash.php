<?php
require_once Load::get_module_dir('usermanagement') . '3rdparty/phpass-0.3/PasswordHash.php';

/**
 * Calculates a hash using PHPPass 0.3 in portable mode
 * 
 * @since 0.6
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Pas3pHash implements IHashAlgorithm {
	/**
	 * Return preconfigured instance of PasswordHash
	 * 
	 * @return PasswordHash03
	 */
	protected function create_pass3_instance() {
		return new PasswordHash03(8, TRUE);
	}
	
	public function hash($source) {
		$o_hash = $this->create_pass3_instance();
		return $o_hash->HashPassword($source);
	}
	
	public function check($source, $hash) {
		$o_hash = $this->create_pass3_instance();
		return $o_hash->CheckPassword($source, $hash);
	}
}