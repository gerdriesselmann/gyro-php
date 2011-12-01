<?php
/**
 * Copy controller implementation to app dir
 *
 * @return Status
 */
function models_contact_check_postconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$ret->merge(SystemUpdateInstaller::copy_to_app(
		'',
		dirname(__FILE__) . '/../data/', 
		'controller', 
		SystemUpdateInstaller::COPY_NO_REPLACE
	));
	return $ret;
}
