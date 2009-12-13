<?php
function core_check_preconditions() {
	$pear_classes = array(
		'Mail',
		'Mail_mime'
	);
	Load::components('installedvalidator');
	$v = new InstalledValidator();
	return $v->validate_pear_modules_are_installed($pear_classes);
}
