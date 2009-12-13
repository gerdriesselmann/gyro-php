<?php
/**
 * Tools for checking if some third party tools are installed
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class InstalledValidator {
	/**
	 * Checks if given PEAR modules are installed
	 *
	 * @attention 
	 *   Usually PEAR modules follow the rule that a _ in a class name maps to 
	 *   a directory in the path name. E.g. module "Text_Diff" maps to file 
	 *   Text/Diff.php. Some PEAR modules however do not follow this convention, 
	 *   Mail_Mime for example. This should be reflected by passing "Mail_mime" 
	 *   in $pear_classes
	 *   
	 * @param $pear_classes Array of Module names, e.g. "Text_Diff", "Mail_mime" etc
	 * @return Status
	 */
	public function validate_pear_modules_are_installed($pear_classes) {
		$ret = new Status();
		foreach(Arr::force($pear_classes, false) as $cls) {
			// A_B_C => A/B/C.php
			// There are PEAR modules that do not follow this convention, Mail_Mime, e.g. 
			// which becomes Mail/mime.php - This should be reflected by passing
			// Mail_mime in $pear_classes
			$file = str_replace('_', '/', $cls) . '.php';
			@include_once($file);
			if ((!class_exists($cls))) {
				$ret->append(tr('PEAR module %mod not installed! Please execute "pear install %mod"', 'systemupdate', array('%mod' => $cls)));
			}
		}
		
		return $ret;
	}
	
	
}
