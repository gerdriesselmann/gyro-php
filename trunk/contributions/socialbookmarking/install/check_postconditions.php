<?php
function socialbookmarking_check_postconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('socialbookmarking') . 'data/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('images', 'css'), SystemUpdateInstaller::COPY_OVERWRITE));	
	return $ret;
}
