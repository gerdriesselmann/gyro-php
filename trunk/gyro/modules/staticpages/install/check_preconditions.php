<?php
function staticpages_check_preconditions() {
	$ret = new Status();
	$root = dirname(__FILE__) . '/../data/';
	Load::components('systemupdateinstaller');
	$ret->merge(SystemUpdateInstaller::copy_to_app(
		'',
		$root, 
		'controller', 
		SystemUpdateInstaller::COPY_NO_REPLACE
	));

	return $ret;
}
